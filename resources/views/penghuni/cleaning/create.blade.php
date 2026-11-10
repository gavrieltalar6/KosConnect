@extends('layouts.app')
@section('title', 'Ajukan Jadwal Kebersihan')
@section('header', 'Ajukan Jadwal Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
    <form action="{{ route('penghuni.cleaning.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Tanggal</label>
            <input type="date" name="requested_date" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Waktu (HH:MM)</label>
            <input type="time" name="requested_time" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Status Keberadaan</label>
            <select name="presence_status" class="w-full border rounded p-2">
                <option value="occupied">Di Tempat (Occupied)</option>
                <option value="vacant">Tidak di Tempat (Vacant)</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Catatan (Opsional)</label>
            <textarea name="note" class="w-full border rounded p-2"></textarea>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Ajukan</button>
    </form>
</div>
@endsection
