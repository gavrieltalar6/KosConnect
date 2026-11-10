@extends('layouts.app')
@section('title', 'Buat Laporan Kerusakan')
@section('header', 'Buat Laporan Kerusakan')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-lg mx-auto">
    <form action="{{ route('penghuni.damage_reports.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Judul</label>
            <input type="text" name="title" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Deskripsi</label>
            <textarea name="description" class="w-full border rounded p-2" rows="4" required></textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('penghuni.damage_reports.index') }}" class="text-gray-600 hover:underline">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kirim Laporan</button>
        </div>
    </form>
</div>
@endsection
