<?php

namespace App\Console;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Http;
use App\Models\Invoice;
use App\Models\ReminderLog;
use App\Enums\InvoiceStatus;
use App\Enums\ReminderType;
use App\Enums\ReminderStatus;
use Carbon\Carbon;

// Menjalankan scheduler setiap hari pukul 08:00
Schedule::call(function () {
    $now = Carbon::now('Asia/Jakarta');

    // Cari invoice yang belum lunas
    $invoices = Invoice::where('status', InvoiceStatus::UNPAID)
        ->whereIn('due_date', [$now->copy()->addDays(3)->format('Y-m-d'), $now->copy()->addDays(1)->format('Y-m-d')])
        ->get();

    foreach ($invoices as $invoice) {
        $type = $invoice->due_date->isSameDay($now->copy()->addDays(3)) ? ReminderType::H_MINUS_3 : ReminderType::H_MINUS_1;

        // Cek apakah reminder untuk tipe ini sudah dikirim
        $alreadySent = ReminderLog::where('invoice_id', $invoice->id)
            ->where('type', $type)
            ->where('status', ReminderStatus::SENT)
            ->exists();

        if (!$alreadySent) {
            $message = "Halo, {$invoice->tenant->full_name}. Tagihan kos Anda untuk periode {$invoice->period} akan jatuh tempo pada {$invoice->due_date->format('d-m-Y')}. Mohon segera lakukan pembayaran.";

            // Integrasi Telegram
            $token = config('services.telegram.bot_token');
            $chatId = $invoice->tenant->telegram_chat_id;

            if ($token && $chatId) {
                $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

                if ($response->successful()) {
                    ReminderLog::create([
                        'invoice_id' => $invoice->id,
                        'type' => $type,
                        'status' => ReminderStatus::SENT,
                        'sent_at' => now(),
                    ]);
                } else {
                    ReminderLog::create([
                        'invoice_id' => $invoice->id,
                        'type' => $type,
                        'status' => ReminderStatus::FAILED,
                        'error_message' => $response->body(),
                    ]);
                }
            }
        }
    }
})->dailyAt('08:00');
