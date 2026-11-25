@extends('layouts.app')
@section('title', 'Ajukan Jadwal Kebersihan')
@section('header', 'Ajukan Jadwal Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
    <form action="{{ route('penghuni.cleaning.store') }}" method="POST">
    @csrf

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
            <strong>Pengajuan gagal!</strong>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif
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

@if (session('error'))
    <div class="mb-4 rounded bg-red-100 p-3 text-red-700">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded bg-red-100 p-3 text-red-700">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
</div>
@endsection