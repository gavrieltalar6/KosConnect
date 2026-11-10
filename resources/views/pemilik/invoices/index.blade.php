@extends('layouts.app')
@section('title', 'Manajemen Tagihan')
@section('header', 'Daftar Tagihan')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Semua Tagihan</h3>
        <a href="{{ route('pemilik.invoices.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Buat Tagihan Baru</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penghuni</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->tenant->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->period }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->due_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status->value === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($invoice->status->value) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($invoice->status->value === 'unpaid')
                                <a href="{{ route('pemilik.invoices.payment.create', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Bayar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data tagihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
