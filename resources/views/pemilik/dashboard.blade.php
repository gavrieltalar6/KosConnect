@extends('layouts.app')
@section('title', 'Dashboard Pemilik')
@section('header', 'Dashboard Pemilik')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kamar -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Kamar</span>
                    <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalRooms }}</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-emerald-600 font-semibold">{{ $occupiedRooms }} terisi</span> &bull; 
                        <span class="text-indigo-600 font-semibold">{{ $availableRooms }} kosong</span>
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Penghuni -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Penghuni Aktif</span>
                    <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $activeTenants }}</h4>
                    <p class="text-xs text-gray-400 mt-1">Terdaftar di sistem</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tagihan Belum Lunas -->
        <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tagihan Tertunda</span>
                    <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $unpaidInvoices }}</h4>
                    <p class="text-xs text-amber-600 mt-1 font-medium">Perlu penagihan</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Laporan Kerusakan Aktif -->
        <a href="{{ route('pemilik.damage_reports.index') }}" 
           class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs hover:border-indigo-300 transition block group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider group-hover:text-indigo-600 transition">Kerusakan Aktif</span>
                    <h4 class="text-2xl font-bold text-rose-600 mt-1">{{ $activeDamageReports }}</h4>
                    <p class="text-xs text-indigo-600 mt-1 font-semibold group-hover:underline">Pantau laporan &rarr;</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Monitoring Kerusakan Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Laporan Kerusakan Fasilitas Terbaru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar laporan fasilitas kos yang baru masuk atau sedang ditangani pengurus.</p>
            </div>
            <a href="{{ route('pemilik.damage_reports.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Lihat Seluruh Laporan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/75">
                        <th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Kendala</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($recentDamageReports as $report)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $report->tenant->full_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($report->location_type === 'own_room')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    Kamar {{ $report->room->room_number ?? $report->room->number ?? '-' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-50 text-yellow-700">
                                    Ruang Umum
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $report->title }}</div>
                            <div class="text-xs text-gray-500 line-clamp-1">{{ $report->description }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $statusVal = is_string($report->status) ? $report->status : $report->status?->value;
                            @endphp
                            @if($statusVal === 'open')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @elseif($statusVal === 'in_progress')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    Diproses
                                </span>
                            @elseif($statusVal === 'resolved')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ ucfirst($statusVal ?? '-') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-gray-400 whitespace-nowrap">
                            {{ $report->created_at ? $report->created_at->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">
                            Tidak ada riwayat laporan kerusakan saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
