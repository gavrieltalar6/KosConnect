<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramBotService $bot): JsonResponse
    {
        $secret = config('services.telegram.webhook_secret');

        // Telegram mengirim secret yang kita daftarkan saat setWebhook lewat header ini
        if ($secret && ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        $bot->handleUpdate($request->all());

        // Selalu 200 supaya Telegram tidak mengulang kirim update yang sama
        return response()->json(['ok' => true]);
    }
}
