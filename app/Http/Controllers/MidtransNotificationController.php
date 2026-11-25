<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        if (! $midtrans->isValidSignature($payload)) {
            Log::warning('Midtrans notification: signature tidak valid', ['order_id' => $payload['order_id'] ?? null]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $result = $midtrans->handleNotification($payload);

        Log::info('Midtrans notification diproses', ['order_id' => $payload['order_id'], 'result' => $result]);

        // Selalu 200 untuk notifikasi sah supaya Midtrans tidak mengulang terus-menerus
        return response()->json(['message' => 'OK', 'result' => $result]);
    }
}
