@extends('layouts.app')

@section('title', 'Laporan Kebersihan')
@section('header', 'Laporan Seluruh Kebersihan Kamar')

@section('content')

{{-- RINGKASAN STATISTIK --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Pengajuan --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Pengajuan</p>
        <h3 class="text-2xl font-bold text-indigo-600 mt-2">
            {{ $stats['total'] }}
        </h3>
    </div>

    {{-- Selesai --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Selesai</p>
        <h3 class="text-2xl font-bold text-green-600 mt-2">
            {{ $stats['done'] }}
        </h3>
    </div>

    {{-- Menunggu --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Menunggu</p>
        <h3 class="text-2xl font-bold text-yellow-600 mt-2">
            {{ $stats['pending'] }}
        </h3>
    </div>

</div>

{{-- TABEL LAPORAN KEBERSIHAN --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Log Laporan Kebersihan
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Daftar riwayat pembersihan kamar yang telah selesai diproses.</p>
        </div>

        <a href="{{ route('pengurus.cleaning.index') }}"
           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            Lihat Verifikasi Jadwal &rarr;
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead>
                <tr class="bg-gray-50/75">
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Penghuni
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Kamar
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Waktu
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Keberadaan Penghuni
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Catatan
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50/75 transition-colors">
                        {{-- Kolom 1: Penghuni --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ $schedule->tenant->full_name ?? 'Penghuni tidak ditemukan' }}
                            </div>
                            @if($schedule->tenant?->phone)
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $schedule->tenant->phone }}
                                </div>
                            @endif
                        </td>

                        {{-- Kolom 2: Kamar --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                Kamar {{ $schedule->room?->room_number ?? $schedule->tenant?->room?->room_number ?? '-' }}
                            </span>
                        </td>

                        {{-- Kolom 3: Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $schedule->requested_date->translatedFormat('d M Y') }}
                        </td>

                        {{-- Kolom 4: Waktu --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }} WIB
                        </td>

                        {{-- Kolom 5: Keberadaan Penghuni (Di Tempat / Tidak di Tempat) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($schedule->presence_status->value === 'occupied')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Di Tempat
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                    Tidak di Tempat
                                </span>
                            @endif
                        </td>

                        {{-- Kolom 6: Status Pengerjaan --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($schedule->status->value === 'done')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        {{-- Kolom 7: Catatan --}}
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs break-words">
                            {{ $schedule->note ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Belum ada laporan kebersihan yang selesai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $schedules->links() }}
    </div>

</div>

@endsection
