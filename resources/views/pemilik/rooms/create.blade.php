@extends('layouts.app')
@section('title', 'Tambah Kamar')
@section('header', 'Tambah Kamar')
@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Tambah Kamar Baru</div>
        <form action="{{ route('pemilik.rooms.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="room_number" class="block text-sm font-medium">Nomor Kamar</label>
                <input type="text" name="room_number" class="w-full border rounded px-3 py-2" value="{{ old('room_number') }}" required>
            </div>
            <div class="mb-4">
                <label for="floor" class="block text-sm font-medium">Lantai</label>
                <input type="number" name="floor" id="floor" class="w-full border rounded px-3 py-2" value="{{ old('floor') }}">
            </div>
            <div class="mb-4">
                <label for="monthly_price" class="block text-sm font-medium">Harga Bulanan (Rp)</label>
                <input type="number" name="monthly_price" id="monthly_price" class="w-full border rounded px-3 py-2 mt-1" required>
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium">Status</label>
                <select name="status" id="status" class="w-full border rounded px-3 py-2 mt-1" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>{{ $status->getLabel() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium">Deskripsi</label>
                <textarea name="description" id="description" class="w-full border rounded px-3 py-2 mt-1">{{ old('description') }}</textarea>
            </div>
            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-800">Simpan</button>
                <a href="{{ route('pemilik.rooms.index') }}" class="text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
@endsection