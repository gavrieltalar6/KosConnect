@extends('layouts.app')
@section('title', 'Pengingat Iuran')
@section('header', 'Pengingat Iuran Penghuni')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Penghuni -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Penghuni</span>
                    <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalTenants }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 017.75-1.35M16 3.13a4 4 0 010 7.75M12 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reminder Aktif -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Pengingat Aktif</span>
                    <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeReminders }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tagihan Belum Lunas -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Belum Lunas</span>
                    <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ $unpaidInvoicesCount }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Telegram Terhubung -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Telegram Terhubung</span>
                    <h4 class="text-2xl font-bold text-blue-600 mt-1">{{ $telegramLinkedCount }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.674.34l.205-3.05 5.566-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-6">
        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Manajemen Pengingat Iuran Otomatis</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Sistem secara otomatis mengirimkan notifikasi pesan Telegram ke nomor HP/chat ID penghuni pada 
                    <span class="font-semibold text-gray-700">H-7</span>, 
                    <span class="font-semibold text-gray-700">H-1</span>, dan 
                    <span class="font-semibold text-gray-700">Hari H</span> tanggal jatuh tempo bagi yang belum lunas.
                </p>
            </div>
            
            <!-- Legend Status Box -->
            <div class="flex items-center gap-3 bg-gray-50 p-2.5 rounded-lg border border-gray-200 text-xs text-gray-600 self-start sm:self-auto shrink-0">
                <span class="font-semibold text-gray-700">Status Pesan:</span>
                <span class="inline-flex items-center gap-1 font-medium">
                    <span class="w-3 h-3 rounded bg-gray-200 border border-gray-300"></span> Belum
                </span>
                <span class="inline-flex items-center gap-1 font-medium">
                    <span class="w-3 h-3 rounded bg-blue-500"></span> Terkirim
                </span>
                <span class="inline-flex items-center gap-1 font-medium">
                    <span class="w-3 h-3 rounded bg-red-500"></span> Gagal
                </span>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="pt-4 border-t border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pengurus.reminders.index', ['reminder_status' => 'all', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $filterStatus === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua Penghuni ({{ $totalTenants }})
                </a>
                <a href="{{ route('pengurus.reminders.index', ['reminder_status' => 'active', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $filterStatus === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Pengingat Aktif ({{ $activeReminders }})
                </a>
                <a href="{{ route('pengurus.reminders.index', ['reminder_status' => 'inactive', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $filterStatus === 'inactive' ? 'bg-gray-700 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Pengingat Nonaktif
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('pengurus.reminders.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="reminder_status" value="{{ $filterStatus }}">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari penghuni, kamar, HP..." 
                           class="w-64 pl-9 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('pengurus.reminders.index', ['reminder_status' => $filterStatus]) }}" class="px-2.5 py-1.5 text-xs text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table List -->
        <div class="overflow-x-auto border border-gray-200 rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/75">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Penghuni & Kamar
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aktif
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tanggal Jatuh Tempo
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Pesan Terkirim
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($tenantReminderData as $item)
                    @php
                        $tenant = $item['tenant'];
                        $inv = $item['invoice'];
                        $latestInv = $item['latest_invoice'];
                        $isActive = $item['is_active'];
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                        <!-- Penghuni & Kamar -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">
                                        {{ $tenant->full_name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                                        @if($tenant->room)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                Kamar {{ $tenant->room->room_number }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">Tanpa kamar</span>
                                        @endif
                                        <span>•</span>
                                        <span>{{ $tenant->phone }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        @if($tenant->telegram_chat_id)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                                <svg class="w-3 h-3 fill-current text-blue-600" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.674.34l.205-3.05 5.566-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/></svg>
                                                Telegram Terhubung
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Telegram Belum Terhubung
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Aktif (Saklar On-Off) -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <form action="{{ route('pengurus.reminders.toggle', $tenant) }}" method="POST" class="inline-block toggle-form">
                                @csrf
                                @method('PATCH')
                                <label class="relative inline-flex items-center cursor-pointer" title="{{ $isActive ? 'Klik untuk mematikan autoreminder' : 'Klik untuk menyalakan autoreminder' }}">
                                    <input type="checkbox" 
                                           name="is_reminder_active" 
                                           value="1" 
                                           class="sr-only peer toggle-input"
                                           onchange="this.form.submit()"
                                           {{ $isActive ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                                <div class="text-[10px] mt-1 font-semibold {{ $isActive ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                                </div>
                            </form>
                        </td>

                        <!-- Tanggal Jatuh Tempo (Terdekat) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item['due_date'])
                                @php
                                    $dueDate = $item['due_date'];
                                    $isPast = $dueDate->isPast() && !$dueDate->isToday();
                                    $isToday = $dueDate->isToday();
                                    $diffDays = (int) ceil(now()->startOfDay()->diffInDays($dueDate->startOfDay(), false));
                                @endphp
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $dueDate->format('d M Y') }}
                                    </div>
                                    <div class="text-xs mt-0.5">
                                        @if($inv)
                                            @if($isToday)
                                                <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                    Hari ini jatuh tempo!
                                                </span>
                                            @elseif($isPast)
                                                <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-bold bg-red-100 text-red-800">
                                                    Terlambat {{ abs($diffDays) }} hari
                                                </span>
                                            @else
                                                <span class="text-indigo-600 font-medium text-[11px]">
                                                    {{ $diffDays }} hari lagi ({{ $inv->period }})
                                                </span>
                                            @endif
                                            <div class="text-[11px] text-gray-500 font-semibold mt-0.5">
                                                Rp {{ number_format($inv->amount, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Semua Lunas
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ada data tagihan</span>
                            @endif
                        </td>

                        <!-- Pesan Terkirim (3 Box: H-7, H-1, Hari H) -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="inline-flex items-center gap-2 p-1.5 bg-gray-50 rounded-xl border border-gray-200/80">
                                <!-- Box H-7 -->
                                @php
                                    $h7 = $item['box_h7'];
                                    $h7Log = $item['log_h7'];
                                @endphp
                                <div class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs text-center cursor-default
                                    {{ $h7 === 'sent' ? 'bg-blue-600 text-white' : ($h7 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status H-7: {{ $h7 === 'sent' ? 'Sudah terkirim' : ($h7 === 'failed' ? 'Gagal: ' . ($h7Log?->error_message ?? 'Error') : 'Belum terkirim') }}">
                                    H-7
                                </div>

                                <!-- Box H-1 -->
                                @php
                                    $h1 = $item['box_h1'];
                                    $h1Log = $item['log_h1'];
                                @endphp
                                <div class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs text-center cursor-default
                                    {{ $h1 === 'sent' ? 'bg-blue-600 text-white' : ($h1 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status H-1: {{ $h1 === 'sent' ? 'Sudah terkirim' : ($h1 === 'failed' ? 'Gagal: ' . ($h1Log?->error_message ?? 'Error') : 'Belum terkirim') }}">
                                    H-1
                                </div>

                                <!-- Box Hari H -->
                                @php
                                    $hDay = $item['box_hday'];
                                    $hDayLog = $item['log_hday'];
                                @endphp
                                <div class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs text-center cursor-default
                                    {{ $hDay === 'sent' ? 'bg-blue-600 text-white' : ($hDay === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status Hari H: {{ $hDay === 'sent' ? 'Sudah terkirim' : ($hDay === 'failed' ? 'Gagal: ' . ($hDayLog?->error_message ?? 'Error') : 'Belum terkirim') }}">
                                    Hari H
                                </div>
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="inline-flex items-center gap-2">
                                @if($inv)
                                    <!-- Tombol Kirim Pengingat Manual -->
                                    <form action="{{ route('pengurus.reminders.send', $tenant) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Kirimkan pesan pengingat tagihan periode {{ $inv->period }} sekarang ke Telegram {{ $tenant->full_name }}?');">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition hover:scale-105 active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            Kirim Sekarang
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-600 font-semibold px-2.5 py-1 bg-emerald-50 rounded-lg">
                                        Lunas
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">
                                    {{ $search ? 'Tidak ada penghuni yang sesuai pencarian' : 'Belum ada data penghuni aktif' }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                                    {{ $search ? 'Coba gunakan kata kunci lain.' : 'Daftarkan penghuni terlebih dahulu untuk mengaktifkan pengingat tagihan otomatis.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="pt-4 border-t border-gray-100">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
