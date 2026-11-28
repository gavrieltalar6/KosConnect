<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\RoomStatus;
use App\Enums\TenantStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'SB-Mid-server-test';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.midtrans.server_key' => self::SERVER_KEY, 'services.midtrans.client_key' => 'SB-Mid-client-test']);
    }

    private function makeTenantWithInvoice(string $email = 'p@test.com'): array
    {
        $room = Room::create(['room_number' => 'K-' . uniqid(), 'monthly_price' => 750000, 'status' => RoomStatus::AVAILABLE]);
        $user = User::create(['name' => 'Penghuni', 'email' => $email, 'password' => bcrypt('password'), 'role' => Role::PENGGUNI->value, 'is_active' => true]);
        $tenant = Tenant::create([
            'user_id' => $user->id, 'room_id' => $room->id, 'full_name' => 'Penghuni', 'phone' => '0811111111',
            'origin_address' => 'Jakarta', 'guardian_phone' => '0822222222', 'check_in_date' => now(), 'due_day' => 10,
            'status' => TenantStatus::ACTIVE,
        ]);
        $invoice = Invoice::create([
            'tenant_id' => $tenant->id, 'room_id' => $room->id, 'period' => '2026-11', 'amount' => 750000,
            'due_date' => now()->addDays(5), 'status' => InvoiceStatus::UNPAID,
        ]);

        return [$user, $tenant, $invoice];
    }

    private function notification(Invoice $invoice, array $override = []): array
    {
        $payload = array_merge([
            'order_id' => 'KOS-' . $invoice->id . '-ABCD1234',
            'status_code' => '200',
            'gross_amount' => '750000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-' . uniqid(),
            'payment_type' => 'bank_transfer',
            'transaction_time' => '2026-11-24 10:00:00',
            'settlement_time' => '2026-11-24 10:01:00',
        ], $override);

        $payload['signature_key'] = $override['signature_key']
            ?? hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::SERVER_KEY);

        return $payload;
    }

    public function test_valid_settlement_marks_invoice_paid_and_records_payment(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();

        $this->postJson('/midtrans/notification', $this->notification($invoice))
            ->assertOk()->assertJson(['result' => 'paid']);

        $invoice->refresh();
        $this->assertSame(InvoiceStatus::PAID, $invoice->status);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id, 'payment_type' => 'bank_transfer', 'recorded_by' => null,
        ]);
    }

    public function test_duplicate_notification_does_not_create_second_payment(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();
        $payload = $this->notification($invoice);

        $this->postJson('/midtrans/notification', $payload)->assertOk();
        $this->postJson('/midtrans/notification', $payload)->assertOk()->assertJson(['result' => 'already_paid']);

        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_invalid_signature_is_rejected(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();

        $this->postJson('/midtrans/notification', $this->notification($invoice, ['signature_key' => 'salah']))->assertForbidden();

        $this->assertSame(InvoiceStatus::UNPAID, $invoice->fresh()->status);
        $this->assertSame(0, Payment::count());
    }

    public function test_amount_mismatch_does_not_mark_paid(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();

        $this->postJson('/midtrans/notification', $this->notification($invoice, ['gross_amount' => '1000.00']))
            ->assertOk()->assertJson(['result' => 'amount_mismatch']);

        $this->assertSame(InvoiceStatus::UNPAID, $invoice->fresh()->status);
    }

    public function test_pending_does_not_change_invoice(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();

        $this->postJson('/midtrans/notification', $this->notification($invoice, ['transaction_status' => 'pending', 'status_code' => '201']))
            ->assertOk()->assertJson(['result' => 'pending']);

        $this->assertSame(InvoiceStatus::UNPAID, $invoice->fresh()->status);
    }

    public function test_expired_payment_clears_snap_token_so_tenant_can_retry(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();
        $invoice->update(['midtrans_order_id' => 'KOS-' . $invoice->id . '-ABCD1234', 'snap_token' => 'tok', 'snap_token_created_at' => now()]);

        $this->postJson('/midtrans/notification', $this->notification($invoice, ['transaction_status' => 'expire', 'status_code' => '407']))
            ->assertOk()->assertJson(['result' => 'failed']);

        $this->assertNull($invoice->fresh()->snap_token);
        $this->assertSame(InvoiceStatus::UNPAID, $invoice->fresh()->status);
    }

    public function test_unknown_order_id_format_is_ignored_safely(): void
    {
        [, , $invoice] = $this->makeTenantWithInvoice();

        $this->postJson('/midtrans/notification', $this->notification($invoice, ['order_id' => 'ORDER-XYZ']))
            ->assertOk()->assertJson(['result' => 'unknown_invoice']);
    }

    public function test_tenant_cannot_request_snap_token_for_someone_elses_invoice(): void
    {
        [$userA] = $this->makeTenantWithInvoice('a@test.com');
        [, , $invoiceB] = $this->makeTenantWithInvoice('b@test.com');

        $this->actingAs($userA)->postJson(route('penghuni.invoices.snap-token', $invoiceB))->assertForbidden();
    }

    public function test_snap_token_refused_for_paid_invoice(): void
    {
        [$user, , $invoice] = $this->makeTenantWithInvoice();
        $invoice->update(['status' => InvoiceStatus::PAID]);

        $this->actingAs($user)->postJson(route('penghuni.invoices.snap-token', $invoice))->assertStatus(422);
    }

    public function test_tenant_invoice_page_lists_only_own_invoices(): void
    {
        [$userA, , $invoiceA] = $this->makeTenantWithInvoice('a@test.com');
        [, , $invoiceB] = $this->makeTenantWithInvoice('b@test.com');
        $invoiceB->update(['period' => '2099-01']);

        $this->actingAs($userA)->get(route('penghuni.invoices.index'))
            ->assertOk()->assertSee('2026-11')->assertDontSee('2099-01')->assertSee('Bayar Sekarang');
    }
}
