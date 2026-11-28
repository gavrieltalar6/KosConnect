<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook {url? : URL publik HTTPS, default APP_URL}';

    protected $description = 'Daftarkan webhook bot Telegram ke /telegram/webhook (untuk production).';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env');

            return self::FAILURE;
        }

        $base = rtrim($this->argument('url') ?: config('app.url'), '/');
        $url = $base . '/telegram/webhook';

        if (! str_starts_with($url, 'https://')) {
            $this->error("Telegram mewajibkan HTTPS publik. URL: {$url}\nUntuk lokal, pakai: php artisan telegram:poll");

            return self::FAILURE;
        }

        $payload = ['url' => $url, 'allowed_updates' => ['message']];

        if ($secret = config('services.telegram.webhook_secret')) {
            $payload['secret_token'] = $secret;
        }

        $res = Http::post("https://api.telegram.org/bot{$token}/setWebhook", $payload);

        $this->line($res->body());

        return $res->successful() ? self::SUCCESS : self::FAILURE;
    }
}
