@extends('layouts.app')
@section('title', 'Buat Tagihan')
@section('header', 'Buat Tagihan Baru')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pemilik.invoices.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Penghuni</label>
            <select name="tenant_id" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->full_name }} —
                        @if($tenant->room)
                            Kamar {{ $tenant->room->room_number }} (Rp {{ number_format($tenant->room->monthly_price, 0, ',', '.') }})
                        @else
                            Belum ada kamar
                        @endif
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Jumlah tagihan otomatis mengikuti harga sewa kamar penghuni.</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Periode (YYYY-MM)</label>
            <input type="text" name="period" value="{{ old('period', date('Y-m')) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required placeholder="2023-10">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('pemilik.invoices.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection