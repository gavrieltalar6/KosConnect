<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';

    protected $description = 'Terima pesan bot Telegram via long polling (untuk development lokal tanpa webhook).';

    public function handle(TelegramBotService $bot): int
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env');

            return self::FAILURE;
        }

        // getUpdates tidak bisa dipakai bersamaan dengan webhook aktif
        Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        $this->info('Bot mendengarkan pesan... (Ctrl+C untuk berhenti)');
        $offset = 0;

        while (true) {
            try {
                $res = Http::timeout(40)->get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30,
                    'allowed_updates' => json_encode(['message']),
                ]);
            } catch (\Throwable $e) {
                $this->warn('Koneksi gagal: ' . $e->getMessage() . ' (coba lagi 3 detik)');
                sleep(3);

                continue;
            }

            foreach ($res->json('result', []) as $update) {
                $offset = $update['update_id'] + 1;
                $bot->handleUpdate($update);
                $this->line('Update #' . $update['update_id'] . ' diproses');
            }
        }
    }
}
