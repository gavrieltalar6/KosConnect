<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use App\Models\Invoice;
use App\Models\ReminderLog;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramReminderService
{
    /**
     * Kirim pesan pengingat tagihan ke penghuni melalui Telegram.
     */
    public function sendReminder(Invoice $invoice, ReminderType $type, bool $force = false): array
    {
        $invoice->loadMissing(['tenant.room', 'tenant.user']);
        $tenant = $invoice->tenant;

        if (!$tenant) {
            return [
                'success' => false,
                'message' => 'Data penghuni tidak ditemukan.',
            ];
        }

        // Cek apakah invoice sudah lunas
        if ($invoice->status === InvoiceStatus::PAID) {
            return [
                'success' => false,
                'message' => 'Tagihan ini sudah lunas, pengingat tidak perlu dikirim.',
            ];
        }

        // Cek apakah reminder aktif pada penghuni (kecuali manual force)
        if (!$force && !$tenant->is_reminder_active) {
            ReminderLog::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'type' => $type,
                'status' => ReminderStatus::SKIPPED,
                'error_message' => 'Pengingat dinonaktifkan untuk penghuni ini.',
            ]);

            return [
                'success' => false,
                'status' => 'skipped',
                'message' => 'Pengingat dinonaktifkan untuk penghuni ini.',
            ];
        }

        // Susun teks pesan pengingat
        $message = $this->buildReminderMessage($invoice, $type);

        $token = config('services.telegram.bot_token');
        $chatId = $tenant->telegram_chat_id;

        // Jika akun telegram penghuni belum terhubung
        if (!$chatId) {
            $errorMsg = 'Akun Telegram penghuni belum terhubung (Chat ID belum terdaftar).';
            ReminderLog::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'type' => $type,
                'status' => ReminderStatus::FAILED,
                'error_message' => $errorMsg,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $errorMsg,
            ];
        }

        // Jika token bot telegram belum diset di .env
        if (!$token) {
            $errorMsg = 'TELEGRAM_BOT_TOKEN belum dikonfigurasi di sistem (.env).';
            ReminderLog::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'type' => $type,
                'status' => ReminderStatus::FAILED,
                'error_message' => $errorMsg,
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $errorMsg,
            ];
        }

        try {
            $response = Http::timeout(8)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                $log = ReminderLog::create([
                    'invoice_id' => $invoice->id,
                    'tenant_id' => $tenant->id,
                    'type' => $type,
                    'status' => ReminderStatus::SENT,
                    'sent_at' => now(),
                ]);

                return [
                    'success' => true,
                    'status' => 'sent',
                    'message' => 'Pesan pengingat berhasil dikirim melalui Telegram.',
                    'log' => $log,
                ];
            } else {
                $body = $response->body();
                ReminderLog::create([
                    'invoice_id' => $invoice->id,
                    'tenant_id' => $tenant->id,
                    'type' => $type,
                    'status' => ReminderStatus::FAILED,
                    'error_message' => "Telegram API Error ({$response->status()}): {$body}",
                ]);

                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => "Telegram API Error: {$body}",
                ];
            }
        } catch (\Throwable $e) {
            Log::error('TelegramReminderService Exception: ' . $e->getMessage());

            ReminderLog::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'type' => $type,
                'status' => ReminderStatus::FAILED,
                'error_message' => 'Koneksi ke Telegram gagal: ' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Koneksi ke Telegram gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Memformat teks pengingat iuran.
     */
    protected function buildReminderMessage(Invoice $invoice, ReminderType $type): string
    {
        $tenant = $invoice->tenant;
        $roomInfo = $tenant->room ? "Kamar {$tenant->room->room_number}" : 'Kamar Kos';
        $formattedAmount = 'Rp ' . number_format($invoice->amount, 0, ',', '.');
        $dueDate = $invoice->due_date ? $invoice->due_date->format('d M Y') : '-';

        $headline = match ($type) {
            ReminderType::H_MINUS_7 => '🔔 *PENGINGAT IURAN (H-7 JATUH TEMPO)*',
            ReminderType::H_MINUS_1 => '⚠️ *PENGINGAT IURAN (H-1 JATUH TEMPO)*',
            ReminderType::H_DAY => '🚨 *PENGINGAT IURAN (HARI INI JATUH TEMPO)*',
            ReminderType::H_MINUS_3 => '🔔 *PENGINGAT IURAN (H-3 JATUH TEMPO)*',
            ReminderType::MANUAL => '📢 *PEMBERITAHUAN IURAN KOS*',
        };

        $contextText = match ($type) {
            ReminderType::H_MINUS_7 => 'Tagihan kos Anda akan jatuh tempo dalam *7 hari ke depan*.',
            ReminderType::H_MINUS_1 => 'Tagihan kos Anda akan jatuh tempo *besok*. Mohon persiapkan pembayaran Anda.',
            ReminderType::H_DAY => 'Hari ini adalah *hari jatuh tempo* pembayaran iuran kos Anda. Mohon segera selesaikan pembayaran.',
            ReminderType::H_MINUS_3 => 'Tagihan kos Anda akan jatuh tempo dalam *3 hari ke depan*.',
            ReminderType::MANUAL => 'Berikut adalah rincian tagihan kos Anda yang perlu diselesaikan.',
        };

        return "{$headline}\n\n"
            . "Halo, *{$tenant->full_name}* ({$roomInfo})\n\n"
            . "{$contextText}\n\n"
            . "📋 *Rincian Tagihan:*\n"
            . "• Periode: {$invoice->period}\n"
            . "• Jumlah: *{$formattedAmount}*\n"
            . "• Jatuh Tempo: *{$dueDate}*\n\n"
            . "Silakan lakukan pembayaran melalui menu *Tagihan Saya* pada aplikasi KosConnect atau hubungi pengurus kos jika ada pertanyaan.\n\n"
            . "Terima kasih atas kerja samanya! 🙏";
    }

    /**
     * Menjalankan pengiriman otomatis untuk jadwal H-7, H-1, dan Hari H.
     */
    public function sendScheduledReminders(): array
    {
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hMinus1Date = $now->copy()->addDay()->toDateString();
        $hMinus7Date = $now->copy()->addDays(7)->toDateString();

        $results = [
            'h_minus_7' => ['sent' => 0, 'failed' => 0, 'skipped' => 0],
            'h_minus_1' => ['sent' => 0, 'failed' => 0, 'skipped' => 0],
            'h_day' => ['sent' => 0, 'failed' => 0, 'skipped' => 0],
        ];

        // 1. Tagihan H-7 (Jatuh tempo 7 hari lagi)
        $invoicesH7 = Invoice::with(['tenant'])
            ->where('status', InvoiceStatus::UNPAID)
            ->whereDate('due_date', $hMinus7Date)
            ->get();

        foreach ($invoicesH7 as $inv) {
            if (!$this->alreadySent($inv, ReminderType::H_MINUS_7)) {
                $res = $this->sendReminder($inv, ReminderType::H_MINUS_7);
                $statusKey = $res['status'] ?? 'failed';
                $results['h_minus_7'][$statusKey]++;
            }
        }

        // 2. Tagihan H-1 (Jatuh tempo besok)
        $invoicesH1 = Invoice::with(['tenant'])
            ->where('status', InvoiceStatus::UNPAID)
            ->whereDate('due_date', $hMinus1Date)
            ->get();

        foreach ($invoicesH1 as $inv) {
            if (!$this->alreadySent($inv, ReminderType::H_MINUS_1)) {
                $res = $this->sendReminder($inv, ReminderType::H_MINUS_1);
                $statusKey = $res['status'] ?? 'failed';
                $results['h_minus_1'][$statusKey]++;
            }
        }

        // 3. Tagihan Hari H (Jatuh tempo hari ini)
        $invoicesHDay = Invoice::with(['tenant'])
            ->where('status', InvoiceStatus::UNPAID)
            ->whereDate('due_date', $todayDate)
            ->get();

        foreach ($invoicesHDay as $inv) {
            if (!$this->alreadySent($inv, ReminderType::H_DAY)) {
                $res = $this->sendReminder($inv, ReminderType::H_DAY);
                $statusKey = $res['status'] ?? 'failed';
                $results['h_day'][$statusKey]++;
            }
        }

        return $results;
    }

    /**
     * Periksa apakah pengingat untuk tipe ini sudah berhasil dikirim.
     */
    protected function alreadySent(Invoice $invoice, ReminderType $type): bool
    {
        return ReminderLog::where('invoice_id', $invoice->id)
            ->where('type', $type)
            ->where('status', ReminderStatus::SENT)
            ->exists();
    }
}
