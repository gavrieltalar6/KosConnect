@extends('layouts.app')

@section('title', 'Dashboard Pengurus')
@section('header', 'Dashboard Pengurus')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-gray-800">Selamat Datang, Pengurus!</h2>
        <p class="mt-1 text-sm text-gray-500">
            Berikut ringkasan aktivitas pengelolaan kos hari ini.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Jadwal Kebersihan --}}
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="rounded-lg bg-indigo-100 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14H4V6a1 1 0 011-1z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-400">Aktif</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Jadwal Kebersihan</p>
            <h3 class="mt-1 text-3xl font-bold text-gray-800">
                {{ $activeCleaningSchedules }}
            </h3>
            <p class="mt-2 text-xs text-gray-400">Menunggu dan disetujui</p>
        </div>

        {{-- Laporan Kerusakan --}}
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="rounded-lg bg-amber-100 p-3">
                    <svg class="h-6 w-6 text-amber-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 3.9L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-400">Aktif</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Laporan Kerusakan</p>
            <h3 class="mt-1 text-3xl font-bold text-gray-800">
                {{ $activeDamageReports }}
            </h3>
            <p class="mt-2 text-xs text-gray-400">Menunggu dan sedang diproses</p>
        </div>

        {{-- Mendekati Jatuh Tempo --}}
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="rounded-lg bg-blue-100 p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-blue-500">7 Hari</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Mendekati Jatuh Tempo</p>
            <h3 class="mt-1 text-3xl font-bold text-gray-800">
                {{ $upcomingDueInvoices }}
            </h3>
            <p class="mt-2 text-xs text-gray-400">Tagihan belum dibayar</p>
        </div>

        {{-- Terlambat Membayar --}}
        <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="rounded-lg bg-red-100 p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-red-500">Perhatian</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Terlambat Membayar</p>
            <h3 class="mt-1 text-3xl font-bold text-gray-800">
                {{ $overdueTenants }}
            </h3>
            <p class="mt-2 text-xs text-gray-400">Jumlah penghuni unik</p>
        </div>

    </div>
</div>
@endsection
