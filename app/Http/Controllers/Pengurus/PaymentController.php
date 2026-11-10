<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Enums\InvoiceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create(Invoice $invoice)
    {
        // Pastikan tagihan belum lunas
        if ($invoice->status === InvoiceStatus::PAID) {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas.');
        }

        return view('pengurus.payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            Payment::create(array_merge($validated, [
                'invoice_id' => $invoice->id,
                'recorded_by' => Auth::id(),
            ]));

            $invoice->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => $validated['paid_at'],
            ]);
        });

        return redirect()->route('pemilik.invoices.index')->with('success', 'Pembayaran berhasil dicatat.');
    }
}
