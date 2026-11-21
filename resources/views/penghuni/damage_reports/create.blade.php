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

            <!-- Info Kamar Otomatis -->
            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Lokasi Kamar</span>
                        <div class="text-sm font-bold text-gray-900">
                            Kamar {{ $room->room_number }} @if($room->floor) (Lantai {{ $room->floor }}) @endif
                        </div>
                    </div>
                </div>
                <span class="text-xs text-indigo-700 bg-indigo-100/70 px-2.5 py-1 rounded-md font-medium">
                    Kamar Anda
                </span>
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
@endsection
