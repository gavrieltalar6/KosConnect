@extends('layouts.app')
@section('title', 'Buat Tagihan')
@section('header', 'Buat Tagihan Baru')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    <form action="{{ route('pemilik.invoices.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Penghuni</label>
            <select name="tenant_id" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Periode (YYYY-MM)</label>
            <input type="text" name="period" value="{{ date('Y-m') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required placeholder="2023-10">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jumlah Tagihan (Rp)</label>
            <input type="number" name="amount" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
            <input type="date" name="due_date" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('pemilik.invoices.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
