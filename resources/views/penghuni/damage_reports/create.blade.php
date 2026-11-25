@extends('layouts.app')
@section('title', 'Buat Laporan Kerusakan')
@section('header', 'Buat Laporan Kerusakan Fasilitas')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Link -->
    <div>
        <a href="{{ route('penghuni.damage_reports.index') }}" 
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            &larr; Kembali ke Daftar Laporan
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">Form Laporan Kerusakan</h3>
            <p class="text-sm text-gray-500 mt-1">
                Jelaskan kendala fasilitas yang Anda alami dengan rinci agar pengurus dapat segera melakukan perbaikan.
            </p>
        </div>

        <form action="{{ route('penghuni.damage_reports.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- Pilihan Lokasi Kerusakan -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                    Lokasi Kerusakan <span class="text-red-500">*</span>
                </label>

                <div class="space-y-2.5">
                    <!-- Opsi: Kamar Sendiri -->
                    <label id="label-own-room"
                           class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-150
                                  {{ old('location_type', 'own_room') === 'own_room' ? 'border-indigo-500 bg-indigo-50/60' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/30' }}">
                        <input type="radio" 
                               name="location_type" 
                               value="own_room"
                               id="loc-own-room"
                               {{ old('location_type', 'own_room') === 'own_room' ? 'checked' : '' }}
                               class="mt-0.5 accent-indigo-600 w-4 h-4 shrink-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Kamar Sendiri</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Kamar <span class="font-semibold text-indigo-700">{{ $room->room_number }}</span>
                                @if($room->floor) (Lantai {{ $room->floor }}) @endif — kamar yang Anda tempati
                            </p>
                        </div>
                    </label>

                    <!-- Opsi: Ruangan Bersama -->
                    <label id="label-shared-area"
                           class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-150
                                  {{ old('location_type') === 'shared_area' ? 'border-indigo-500 bg-indigo-50/60' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/30' }}">
                        <input type="radio" 
                               name="location_type" 
                               value="shared_area"
                               id="loc-shared-area"
                               {{ old('location_type') === 'shared_area' ? 'checked' : '' }}
                               class="mt-0.5 accent-indigo-600 w-4 h-4 shrink-0">
                        <div class="w-full">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-900">Ruangan Bersama / Area Umum</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">Dapur bersama, kamar mandi bersama, koridor, teras, dll.</p>

                            <!-- Field detail lokasi (hanya tampil saat ruangan bersama dipilih) -->
                            <div id="shared-area-field" class="{{ old('location_type') === 'shared_area' ? '' : 'hidden' }} mt-3">
                                <label for="shared_area_detail" class="block text-xs font-semibold text-gray-600 mb-1">
                                    Nama / Keterangan Lokasi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="shared_area_detail"
                                       name="shared_area_detail"
                                       value="{{ old('shared_area_detail') }}"
                                       placeholder="Contoh: Kamar Mandi Lantai 2, Dapur Bersama, Koridor Depan..."
                                       maxlength="255"
                                       class="w-full text-sm px-3.5 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('shared_area_detail') ? 'border-red-400 bg-red-50/30' : 'border-gray-300 focus:border-indigo-500' }}">
                                @error('shared_area_detail')
                                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </label>
                </div>

                @error('location_type')
                    <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Judul -->
            <div>
                <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Judul Kerusakan <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       placeholder="Contoh: Kran air wastafel bocor, Saklar lampu tidak menyala" 
                       required 
                       class="w-full text-sm px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('title') ? 'border-red-400 bg-red-50/30' : 'border-gray-300 focus:border-indigo-500' }}">
                @error('title')
                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="5" 
                          placeholder="Jelaskan detail masalah, bagian mana yang rusak, dan sejak kapan terjadi kendala..." 
                          required 
                          class="w-full text-sm px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('description') ? 'border-red-400 bg-red-50/30' : 'border-gray-300 focus:border-indigo-500' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Maksimal 2000 karakter.</p>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('penghuni.damage_reports.index') }}" 
                   class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Kirim Laporan Kerusakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const ownRoomRadio    = document.getElementById('loc-own-room');
    const sharedAreaRadio = document.getElementById('loc-shared-area');
    const sharedField     = document.getElementById('shared-area-field');
    const sharedInput     = document.getElementById('shared_area_detail');
    const labelOwnRoom    = document.getElementById('label-own-room');
    const labelShared     = document.getElementById('label-shared-area');

    function applyStyles(selected) {
        const activeClasses   = ['border-indigo-500', 'bg-indigo-50/60'];
        const inactiveClasses = ['border-gray-200', 'bg-white'];

        if (selected === 'own_room') {
            labelOwnRoom.classList.add(...activeClasses);
            labelOwnRoom.classList.remove(...inactiveClasses);
            labelShared.classList.remove(...activeClasses);
            labelShared.classList.add(...inactiveClasses);
            sharedField.classList.add('hidden');
            sharedInput.value = '';
            sharedInput.removeAttribute('required');
        } else {
            labelShared.classList.add(...activeClasses);
            labelShared.classList.remove(...inactiveClasses);
            labelOwnRoom.classList.remove(...activeClasses);
            labelOwnRoom.classList.add(...inactiveClasses);
            sharedField.classList.remove('hidden');
            sharedInput.setAttribute('required', 'required');
        }
    }

    ownRoomRadio.addEventListener('change', function () {
        if (this.checked) applyStyles('own_room');
    });

    sharedAreaRadio.addEventListener('change', function () {
        if (this.checked) applyStyles('shared_area');
    });

    // On page load — restore old() state if validation failed
    if (sharedAreaRadio.checked) {
        applyStyles('shared_area');
    } else {
        applyStyles('own_room');
    }
})();
</script>
@endsection
