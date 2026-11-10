@extends('layouts.app')
@section('title', 'Ubah Jadwal Kebersihan')
@section('header', 'Ubah Jadwal Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
    <form action="{{ route('penghuni.cleaning.update', $schedule) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Tanggal</label>
            <input type="date" name="requested_date" class="w-full border rounded p-2" value="{{ old('requested_date', $schedule->requested_date->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Waktu (HH:MM)</label>
            <input type="time" name="requested_time" class="w-full border rounded p-2" value="{{ old('requested_time', \Carbon\Carbon::parse($schedule->requested_time)->format('H:i')) }}" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Status Keberadaan</label>
            <select name="presence_status" class="w-full border rounded p-2">
                <option value="occupied" {{ old('presence_status', $schedule->presence_status->value ?? $schedule->presence_status) === 'occupied' ? 'selected' : '' }}>Di Tempat (Occupied)</option>
                <option value="vacant" {{ old('presence_status', $schedule->presence_status->value ?? $schedule->presence_status) === 'vacant' ? 'selected' : '' }}>Tidak di Tempat (Vacant)</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Catatan (Opsional)</label>
            <textarea name="note" class="w-full border rounded p-2">{{ old('note', $schedule->note) }}</textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('penghuni.cleaning.index') }}" class="text-gray-600 hover:underline">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
