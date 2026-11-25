<?php

namespace App\Http\Controllers\Penghuni;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        $invoices = $tenant
            ? Invoice::with('room', 'payments')->where('tenant_id', $tenant->id)->orderByDesc('due_date')->get()
            : collect();

        $totalUnpaid = $invoices->where('status', InvoiceStatus::UNPAID)->sum('amount');

        return view('penghuni.invoices.index', [
            'invoices' => $invoices,
            'totalUnpaid' => $totalUnpaid,
            'clientKey' => config('services.midtrans.client_key'),
            'snapJsUrl' => config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js',
        ]);
    }

    public function snapToken(Invoice $invoice, MidtransService $midtrans): JsonResponse
    {
        abort_unless($invoice->canBeAccessedBy(Auth::user()), 403);

        if ($invoice->status === InvoiceStatus::PAID) {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 422);
        }

        try {
            return response()->json(['token' => $midtrans->getSnapToken($invoice)]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Gagal menyiapkan pembayaran. Coba lagi beberapa saat.'], 502);
        }
    }
}
