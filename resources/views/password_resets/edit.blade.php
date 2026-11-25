@extends('layouts.app')
@section('title', 'Reset Password - ' . $user->name)
@section('header', 'Reset Password Akun Pengguna')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Kartu Informasi Akun Target -->
    <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">
        <h3 class="text-md font-semibold text-gray-800 mb-3 text-center">Informasi Akun yang Akan Direset</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 block">Nama Lengkap:</span>
                <span class="font-medium text-gray-900 text-base">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block">Email Login:</span>
                <span class="font-medium text-gray-900">{{ $user->email }}</span>
            </div>
            <div>
                <span class="text-gray-500 block">Peran (Role):</span>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->role->value === 'pengurus' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($user->role->value) }}
                </span>
            </div>
            <div>
                <span class="text-gray-500 block">Kamar / Detail:</span>
                <span class="font-medium text-gray-900">
                    {{ $user->tenant?->room ? 'Kamar ' . $user->tenant->room->room_number : ($user->tenant ? 'Belum ada kamar' : 'Staf Kos') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Form Ganti Password (Input 2x) -->
    <div class="bg-white p-6 rounded shadow">
        <div class="mb-4 text-center">
            <h4 class="text-lg font-semibold text-gray-800">Setel Password Baru</h4>
            <p class="text-sm text-gray-600 mt-1">
                Masukkan password baru sebanyak <strong>2 kali</strong> untuk verifikasi dan mencegah kesalahan input.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password_resets.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Input Password Baru (1x) -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password Baru <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    minlength="6"
                    placeholder="Masukkan minimal 6 karakter..." 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2.5 text-sm"
                >
                <span class="text-xs text-gray-500 mt-1 block">Minimal 6 karakter.</span>
            </div>

            <!-- Input Konfirmasi Password Baru (2x) -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Ulangi Password Baru (Konfirmasi) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    minlength="6"
                    placeholder="Ketik ulang password baru..." 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2.5 text-sm"
                >
                <span class="text-xs text-gray-500 mt-1 block">Pastikan isinya sama persis dengan kolom password di atas.</span>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('password_resets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium shadow-sm transition">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
