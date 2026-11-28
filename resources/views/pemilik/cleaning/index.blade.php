@extends('layouts.app')
@section('title', 'Laporan Kebersihan')
@section('header', 'Monitoring Laporan Kebersihan Kamar')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards Overview (tanpa Dijadwalkan Ulang) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pengajuan -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pengajuan</span>
                    <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalReports }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Menunggu</span>
                    <h4 class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Disetujui</span>
                    <h4 class="text-2xl font-bold text-blue-600 mt-1">{{ $approvedCount }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai</span>
                    <h4 class="text-2xl font-bold text-emerald-600 mt-1">{{ $doneCount }}</h4>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pemilik.cleaning_reports.index', ['status' => 'all', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $statusFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua Status ({{ $totalReports }})
                </a>
                <a href="{{ route('pemilik.cleaning_reports.index', ['status' => 'pending', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('pemilik.cleaning_reports.index', ['status' => 'approved', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $statusFilter === 'approved' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    Disetujui ({{ $approvedCount }})
                </a>
                <a href="{{ route('pemilik.cleaning_reports.index', ['status' => 'done', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $statusFilter === 'done' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Selesai ({{ $doneCount }})
                </a>
                <a href="{{ route('pemilik.cleaning_reports.index', ['status' => 'rejected', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $statusFilter === 'rejected' ? 'bg-red-600 text-white shadow-xs' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                    Ditolak ({{ $rejectedCount }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('pemilik.cleaning_reports.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari penghuni, kamar, catatan..."
                           class="w-64 pl-9 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('pemilik.cleaning_reports.index', ['status' => $statusFilter]) }}" class="px-2.5 py-1.5 text-xs text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/75">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Penghuni
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kamar
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jadwal Diminta
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Keberadaan Penghuni
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Pengurus Peninjau
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Catatan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                            <!-- Penghuni -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-900">
                                    {{ $schedule->tenant->full_name ?? 'Penghuni tidak ditemukan' }}
                                </div>
                                @if($schedule->tenant?->phone)
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $schedule->tenant->phone }}
                                    </div>
                                @endif
                            </td>

                            <!-- Kamar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    Kamar {{ $schedule->room?->room_number ?? $schedule->tenant?->room?->room_number ?? '-' }}
                                </span>
                            </td>

                            <!-- Tanggal & Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $schedule->requested_date->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }} WIB
                                </div>
                            </td>

                            <!-- Keberadaan Penghuni -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->presence_status->value === 'occupied')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        Di Tempat
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        Tidak di Tempat
                                    </span>
                                @endif
                            </td>

                            <!-- Pengurus Peninjau -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->reviewer)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($schedule->reviewer->name, 0, 1)) }}
                                        </div>
                                        <span class="text-xs font-semibold text-gray-800">{{ $schedule->reviewer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ditinjau</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusVal = is_string($schedule->status) ? $schedule->status : $schedule->status?->value;
                                @endphp

                                @if($statusVal === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($statusVal === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Disetujui
                                    </span>
                                @elseif($statusVal === 'done')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @elseif($statusVal === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ ucfirst($statusVal ?? '-') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Catatan -->
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs break-words">
                                {{ $schedule->note ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-base font-semibold text-gray-800">
                                        {{ $search ? 'Tidak ada laporan yang cocok dengan pencarian' : 'Belum ada laporan kebersihan' }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1 max-w-sm">
                                        {{ $search ? 'Coba ganti kata kunci pencarian atau ubah filter status.' : 'Seluruh pengajuan dan riwayat pembersihan kamar penghuni akan tampil di sini.' }}
                                    </p>
                                    @if($search)
                                        <a href="{{ route('pemilik.cleaning_reports.index', ['status' => $statusFilter]) }}" class="mt-3 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Hapus pencarian
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
