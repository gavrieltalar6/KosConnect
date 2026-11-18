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
        // Jumlah bayar TIDAK diterima dari form: selalu mengikuti tagihan,
        // yang nilainya berasal dari harga sewa kamar penghuni.
        $validated = $request->validate([
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $alreadyPaid = DB::transaction(function () use ($validated, $invoice) {
            // Kunci baris tagihan supaya tidak ada pembayaran ganda bersamaan
            $locked = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === InvoiceStatus::PAID) {
                return true;
            }
            $invoice = $locked;

            Payment::create([
                'invoice_id' => $invoice->id,
                'recorded_by' => Auth::id(),
                'amount' => $invoice->amount,
                'paid_at' => $validated['paid_at'],
                'method' => $validated['method'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);

            $invoice->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => $validated['paid_at'],
            ]);

            return false;
        });

        if ($alreadyPaid) {
            return redirect()->route('pemilik.invoices.index')->with('error', 'Tagihan ini sudah lunas.');
        }

        return redirect()->route('pemilik.invoices.index')->with('success', 'Pembayaran berhasil dicatat.');
    }
}