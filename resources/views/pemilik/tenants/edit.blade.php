@extends('layouts.app')
@section('title', 'Edit Penghuni')
@section('header', 'Edit Penghuni: ' . $tenant->full_name)

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pemilik.tenants.update', $tenant) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="full_name" value="{{ old('full_name', $tenant->full_name) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
            <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Alamat Asal</label>
            <textarea name="origin_address" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>{{ old('origin_address', $tenant->origin_address) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">No HP Orang Tua/Wali</label>
            <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $tenant->guardian_phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Pilih Kamar</label>
            <select name="room_id" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                <option value="">-- Tanpa Kamar --</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ old('room_id', $tenant->room_id) == $room->id ? 'selected' : '' }}>
                        Kamar {{ $room->room_number }} (Rp {{ number_format($room->monthly_price, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tanggal Check-In</label>
            <input type="date" name="check_in_date" value="{{ old('check_in_date', $tenant->check_in_date->format('Y-m-d')) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo Bulanan (1-28)</label>
            <input type="number" name="due_day" min="1" max="28" value="{{ old('due_day', $tenant->due_day) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Status Penghuni</label>
            <select name="status" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                <option value="active" {{ old('status', $tenant->status->value) === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ old('status', $tenant->status->value) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('pemilik.tenants.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Perbarui</button>
        </div>
    </form>
</div>
@endsection
