@extends('layouts.app')
@section('title', 'Catat Pembayaran')
@section('header', 'Catat Pembayaran - Tagihan #' . $invoice->id)

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    <div class="mb-4 p-4 bg-gray-50 rounded">
        <p><strong>Penghuni:</strong> {{ $invoice->tenant->full_name }}</p>
        <p><strong>Periode:</strong> {{ $invoice->period }}</p>
        <p><strong>Total Tagihan:</strong> Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
    </div>

    <form action="{{ route('pemilik.invoices.payment.store', $invoice) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
            <input type="number" name="amount" value="{{ $invoice->amount }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tanggal Bayar</label>
            <input type="date" name="paid_at" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
            <input type="text" name="method" class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Contoh: Transfer BCA">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea name="note" class="mt-1 block w-full border border-gray-300 rounded-md p-2"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('pemilik.invoices.index') }}" style="background-color: #6b7280 !important; color: white !important; padding: 8px 16px; border-radius: 4px; font-weight: bold; text-decoration: none; display: inline-block;">Batal</a>
            <button type="submit" style="background-color: #16a34a !important; color: white !important; padding: 8px 16px; border-radius: 4px; font-weight: bold; border: none; cursor: pointer;">Simpan Pembayaran</button>
        </div>
    </form>
</div>
@endsection
