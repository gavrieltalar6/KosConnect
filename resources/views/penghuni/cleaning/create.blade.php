
@extends('layouts.app')

@section('title', 'Ajukan Jadwal Kebersihan')
@section('header', 'Ajukan Jadwal Kebersihan')

@section('content')

<div class="bg-white p-6 rounded shadow max-w-lg mx-auto">

    {{-- NOTIFIKASI GAGAL --}}
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">
            <strong>Pengajuan Gagal!</strong>
            <p class="mt-1">{{ session('error') }}</p>
        </div>
    @endif

    {{-- NOTIFIKASI VALIDASI --}}
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">
            <strong>Pengajuan Gagal!</strong>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penghuni.cleaning.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Tanggal</label>
            <input
                type="date"
                name="requested_date"
                value="{{ old('requested_date') }}"
                class="w-full border rounded p-2"
                required
            >
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Waktu (HH:MM)</label>
            <input
                type="time"
                name="requested_time"
                value="{{ old('requested_time') }}"
                class="w-full border rounded p-2"
                required
            >
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Status Keberadaan</label>
            <select
                name="presence_status"
                class="w-full border rounded p-2"
                required
            >
                <option value="occupied" {{ old('presence_status') === 'occupied' ? 'selected' : '' }}>
                    Di Tempat (Occupied)
                </option>

                <option value="vacant" {{ old('presence_status') === 'vacant' ? 'selected' : '' }}>
                    Tidak di Tempat (Vacant)
                </option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Catatan (Opsional)</label>
            <textarea
                name="note"
                class="w-full border rounded p-2"
            >{{ old('note') }}</textarea>
        </div>

        <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
        >
            Ajukan
        </button>

    </form>
</div>

@endsection
