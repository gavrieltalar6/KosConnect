@extends('layouts.app')
@section('title', 'Profil Pemilik')
@section('header', 'Profil Pemilik')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Kartu Ringkasan Akun -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                Pemilik
            </span>
        </div>
    </div>

    <!-- Form Pengaturan Profil -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="bg-indigo-700 px-6 py-4 flex items-center gap-2.5 text-white">
            <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="text-base font-bold text-white">Pengaturan Akun Pemilik</h4>
                <p class="text-xs text-indigo-100">Perbarui nama, email login, atau password akun Anda.</p>
            </div>
        </div>

        <form action="{{ route('pemilik.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informasi Dasar -->
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="Masukkan nama pemilik..."
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                            Email Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="contoh@email.com"
                        >
                    </div>
                </div>

                <!-- Pembatas Bagian Password -->
                <div class="border-t border-gray-100 pt-5">
                    <div class="mb-4">
                        <h5 class="text-sm font-bold text-gray-800">Ubah Password</h5>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Kosongkan kedua kolom di bawah jika tidak ingin mengubah password. Jika ingin mengubah, masukkan password baru sebanyak <strong>2 kali</strong> dan pastikan sama.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                                Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    minlength="6"
                                    autocomplete="new-password"
                                    placeholder="Minimal 6 karakter..."
                                    class="block w-full pl-3.5 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                >
                                <button type="button" onclick="toggleVisibility('password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer" title="Lihat/Sembunyikan Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                                Ulangi Password Baru
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    minlength="6"
                                    autocomplete="new-password"
                                    placeholder="Ketik ulang password baru..."
                                    class="block w-full pl-3.5 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                >
                                <button type="button" onclick="toggleVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer" title="Lihat/Sembunyikan Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p id="passwordMatchHint" class="text-xs mt-2 hidden"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('pemilik.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95">
                    Kembali
                </a>
                <button type="submit" id="submitProfileBtn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transform transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.classList.toggle('text-indigo-600', isPassword);
        btn.classList.toggle('text-gray-400', !isPassword);
    }

    const pwInput = document.getElementById('password');
    const pwConfirmInput = document.getElementById('password_confirmation');
    const pwHint = document.getElementById('passwordMatchHint');

    function checkPasswordMatch() {
        const pw = pwInput.value;
        const confirm = pwConfirmInput.value;

        if (!pw && !confirm) {
            pwHint.classList.add('hidden');
            pwConfirmInput.setCustomValidity('');
            return;
        }

        pwHint.classList.remove('hidden');

        if (pw === confirm && pw.length >= 6) {
            pwHint.textContent = 'Password cocok.';
            pwHint.className = 'text-xs mt-2 font-medium text-emerald-600';
            pwConfirmInput.setCustomValidity('');
        } else if (pw !== confirm) {
            pwHint.textContent = 'Konfirmasi password belum sama.';
            pwHint.className = 'text-xs mt-2 font-medium text-red-600';
            pwConfirmInput.setCustomValidity('Konfirmasi password harus sama dengan password baru.');
        } else {
            pwHint.textContent = 'Password minimal 6 karakter.';
            pwHint.className = 'text-xs mt-2 font-medium text-amber-600';
            pwConfirmInput.setCustomValidity('');
        }
    }

    pwInput.addEventListener('input', checkPasswordMatch);
    pwConfirmInput.addEventListener('input', checkPasswordMatch);
</script>
@endsection
