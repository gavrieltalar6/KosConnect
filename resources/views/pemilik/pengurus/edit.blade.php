@extends('layouts.app')
@section('title', 'Edit Pengurus')
@section('header', 'Edit Pengurus')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Edit Pengurus</h3>
        <a href="{{ route('pemilik.pengurus.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-600">Kembali</a>
    </div>

    <form action="{{ route('pemilik.pengurus.update', $user) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pengurus</label>
            <input type="text" name="full_name" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ $user->full_name }}" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email Pengurus</label>
            <input type="email" name="email" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ $user->email }}" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status Pengurus</label>
            <select name="is_active" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
            <a href="{{ route('pemilik.pengurus.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-600">Batal</a>
        </div>
    </form>
</div>
@endsection