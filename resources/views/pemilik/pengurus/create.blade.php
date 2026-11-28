@extends('layouts.app')
@section('title', 'Tambah Pengurus')
@section('header', 'Tambah Pengurus Baru')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pemilik.pengurus.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email (Untuk Login)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Password Akun Pengurus</label>
                <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required minlength="6">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required minlength="6">
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('pemilik.pengurus.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
