<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    /** Snap token Midtrans berlaku 24 jam; dipakai ulang hanya selama masih segar. */
    private const TOKEN_REUSE_HOURS = 12;

    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Format order_id: KOS-{invoice_id}-{random}. ID tagihan bisa dibaca balik dari order_id,
     * jadi notifikasi untuk percobaan bayar lama pun tetap bisa dicocokkan ke tagihannya.
     */
    public static function invoiceIdFromOrderId(string $orderId): ?int
    {
        return preg_match('/^KOS-(\d+)-[A-Za-z0-9]+$/', $orderId, $m) ? (int) $m[1] : null;
    }

    public function getSnapToken(Invoice $invoice): string
    {
        if (
            $invoice->snap_token
            && $invoice->snap_token_created_at
            && $invoice->snap_token_created_at->gt(now()->subHours(self::TOKEN_REUSE_HOURS))
        ) {
            return $invoice->snap_token;
        }

        $invoice->loadMissing('tenant.user', 'room');
        $tenant = $invoice->tenant;

        $orderId = 'KOS-' . $invoice->id . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        $token = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $orderId,
                // Jumlah selalu dari tagihan di server, bukan dari input klien
                'gross_amount' => (int) round((float) $invoice->amount),
            ],
            'item_details' => [[
                'id' => 'INV-' . $invoice->id,
                'price' => (int) round((float) $invoice->amount),
                'quantity' => 1,
                'name' => mb_substr('Iuran kos ' . $invoice->period . ($invoice->room ? ' - ' . $invoice->room->room_number : ''), 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $tenant->full_name,
                'email' => $tenant->user?->email,
                'phone' => $tenant->phone,
            ],
        ]);

        $invoice->update([
            'midtrans_order_id' => $orderId,
            'snap_token' => $token,
            'snap_token_created_at' => now(),
        ]);

        return $token;
    }

    public function isValidSignature(array $payload): bool
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $key) {
            if (! isset($payload[$key]) || ! is_string($payload[$key])) {
                return false;
            }
        }

       $expected = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('services.midtrans.signature_key'));

        return hash_equals($expected, $payload['signature_key']);
    }

    /**
     * Proses notifikasi yang SUDAH lolos verifikasi signature.
     *
     * @return string ringkasan hasil: paid | already_paid | pending | failed | amount_mismatch | unknown_invoice | ignored
     */
    public function handleNotification(array $payload): string
    {
        $orderId = $payload['order_id'];
        $invoiceId = self::invoiceIdFromOrderId($orderId);

        if (! $invoiceId) {
            return 'unknown_invoice';
        }

        $status = $payload['transaction_status'] ?? '';
        $fraud = $payload['fraud_status'] ?? 'accept';

        $isSuccess = $status === 'settlement' || ($status === 'capture' && $fraud === 'accept');
        $isFailed = in_array($status, ['deny', 'cancel', 'expire', 'failure'], true);

        return DB::transaction(function () use ($invoiceId, $orderId, $payload, $isSuccess, $isFailed, $status) {
            $invoice = Invoice::whereKey($invoiceId)->lockForUpdate()->first();

            if (! $invoice) {
                return 'unknown_invoice';
            }

            if ($isSuccess) {
                if ($invoice->status === InvoiceStatus::PAID) {
                    return 'already_paid';
                }

                // Pastikan nominal yang dibayar sama dengan tagihan
                if ((int) round((float) ($payload['gross_amount'] ?? 0)) !== (int) round((float) $invoice->amount)) {
                    return 'amount_mismatch';
                }

                $paidAt = isset($payload['settlement_time']) ? $payload['settlement_time'] : ($payload['transaction_time'] ?? now());

                Payment::create([
                    'invoice_id' => $invoice->id,
                    'recorded_by' => null,
                    'amount' => $invoice->amount,
                    'paid_at' => $paidAt,
                    'method' => 'Midtrans' . (isset($payload['payment_type']) ? ' - ' . $payload['payment_type'] : ''),
                    'note' => 'Dibayar online via Midtrans',
                    'midtrans_order_id' => $orderId,
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $payload['payment_type'] ?? null,
                ]);

                $invoice->update([
                    'status' => InvoiceStatus::PAID,
                    'paid_at' => $paidAt,
                    'snap_token' => null,
                    'snap_token_created_at' => null,
                ]);

                return 'paid';
            }

            if ($isFailed) {
                // Bebaskan token supaya penghuni bisa mencoba bayar lagi dengan order baru
                if ($invoice->midtrans_order_id === $orderId) {
                    $invoice->update(['snap_token' => null, 'snap_token_created_at' => null]);
                }

                return 'failed';
            }

            return $status === 'pending' ? 'pending' : 'ignored';
        });
    }
}
