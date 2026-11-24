<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\Role;
use App\Enums\TenantStatus;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomOccupancyAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenant(Room $room, bool $userActive = true, TenantStatus $status = TenantStatus::ACTIVE): Tenant
    {
        $user = User::create([
            'name' => 'Penghuni',
            'email' => uniqid('p') . '@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PENGGUNI->value,
            'is_active' => $userActive,
        ]);

        return Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'full_name' => 'Penghuni',
            'phone' => '0811111111',
            'origin_address' => 'Jakarta',
            'guardian_phone' => '0822222222',
            'check_in_date' => now(),
            'due_day' => 10,
            'status' => $status,
        ]);
    }

    private function makeOwner(): User
    {
        return User::create([
            'name' => 'Pemilik',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => true,
        ]);
    }

    public function test_room_becomes_occupied_when_active_tenant_exists(): void
    {
        $room = Room::create(['room_number' => 'K-10', 'monthly_price' => 500000, 'status' => RoomStatus::AVAILABLE]);

        $this->makeTenant($room);
        $room->syncOccupancyStatus();

        $this->assertEquals(RoomStatus::OCCUPIED, $room->fresh()->status);
    }

    public function test_room_becomes_available_when_tenant_is_inactive_or_user_disabled(): void
    {
        $room = Room::create(['room_number' => 'K-11', 'monthly_price' => 500000, 'status' => RoomStatus::OCCUPIED]);
        $this->makeTenant($room, userActive: false);

        $room->syncOccupancyStatus();
        $this->assertEquals(RoomStatus::AVAILABLE, $room->fresh()->status);

        $room2 = Room::create(['room_number' => 'K-12', 'monthly_price' => 500000, 'status' => RoomStatus::OCCUPIED]);
        $this->makeTenant($room2, status: TenantStatus::INACTIVE);

        $room2->syncOccupancyStatus();
        $this->assertEquals(RoomStatus::AVAILABLE, $room2->fresh()->status);
    }

    public function test_maintenance_room_without_tenant_is_left_alone(): void
    {
        $room = Room::create(['room_number' => 'K-13', 'monthly_price' => 500000, 'status' => RoomStatus::MAINTENANCE]);

        $room->syncOccupancyStatus();

        $this->assertEquals(RoomStatus::MAINTENANCE, $room->fresh()->status);
    }

    public function test_invoice_amount_and_room_follow_tenant_room_price(): void
    {
        $owner = $this->makeOwner();
        $room = Room::create(['room_number' => 'K-20', 'monthly_price' => 725000, 'status' => RoomStatus::OCCUPIED]);
        $tenant = $this->makeTenant($room);

        $this->actingAs($owner)->post(route('pemilik.invoices.store'), [
            'tenant_id' => $tenant->id,
            'period' => '2026-11',
            'due_date' => '2026-11-15',
            'amount' => 1, // diabaikan, harus ikut harga kamar
        ]);

        $invoice = Invoice::where('tenant_id', $tenant->id)->firstOrFail();
        $this->assertEquals(725000, (float) $invoice->amount);
        $this->assertEquals($room->id, $invoice->room_id);
        $this->assertEquals(InvoiceStatus::UNPAID, $invoice->status);
    }

    public function test_invoice_is_refused_when_tenant_has_no_room(): void
    {
        $owner = $this->makeOwner();
        $room = Room::create(['room_number' => 'K-21', 'monthly_price' => 500000]);
        $tenant = $this->makeTenant($room);
        $tenant->update(['room_id' => null]);

        $this->actingAs($owner)->post(route('pemilik.invoices.store'), [
            'tenant_id' => $tenant->id,
            'period' => '2026-11',
            'due_date' => '2026-11-15',
        ]);

        $this->assertDatabaseCount('invoices', 0);
    }
}