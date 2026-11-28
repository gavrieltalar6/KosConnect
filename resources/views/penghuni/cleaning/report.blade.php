@extends('layouts.app')
@section('title', 'Laporan Kebersihan')
@section('header', 'Laporan Kebersihan Kamar')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Riwayat Laporan Kebersihan</h3>

        <a href="{{ route('penghuni.cleaning.index') }}"
           class="text-indigo-600 hover:text-indigo-900 font-medium">
            Lihat Jadwal Kebersihan &rarr;
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Request
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Waktu
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Catatan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $schedule->requested_date->format('d-m-Y') }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $schedule->note ?? '-' }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($schedule->status->value === 'done')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Done
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                        @endif
                    </td>

                    {{-- TOMBOL DETAIL --}}
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <button
                            type="button"
                            onclick="document.getElementById('detailModal-{{ $schedule->id }}').showModal()"
                            style="
                                background-color: #4f46e5 !important;
                                color: #ffffff !important;
                                border: 1px solid #4338ca !important;
                                padding: 9px 18px !important;
                                border-radius: 8px !important;
                                font-size: 12px !important;
                                font-weight: 700 !important;
                                display: inline-block !important;
                                cursor: pointer !important;
                                min-width: 75px;
                                text-align: center;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.15);
                            "
                            onmouseover="this.style.backgroundColor='#4338ca'"
                            onmouseout="this.style.backgroundColor='#4f46e5'">
                            Detail
                        </button>
                    </td>
                </tr>

                {{-- POPUP DETAIL LAPORAN --}}
                <dialog id="detailModal-{{ $schedule->id }}"
                        class="rounded-2xl shadow-xl p-0 w-full max-w-md backdrop:bg-black/50">

                    <div class="p-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-800">
                                Detail Laporan Kebersihan
                            </h3>

                            <button
                                type="button"
                                onclick="document.getElementById('detailModal-{{ $schedule->id }}').close()"
                                class="text-gray-400 hover:text-gray-600 text-2xl">
                                &times;
                            </button>
                        </div>

                        <div class="space-y-4 text-sm">

                            <div>
                                <p class="text-gray-500 mb-1">Tanggal Pengajuan</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $schedule->requested_date->format('d-m-Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 mb-1">Waktu Pengajuan</p>
                                <p class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 mb-1">Catatan Penghuni</p>
                                <div class="bg-gray-50 rounded-lg p-3 text-gray-700">
                                    {{ $schedule->note ?: 'Tidak ada catatan.' }}
                                </div>
                            </div>

                            <div>
                                <p class="text-gray-500 mb-1">Status Pengajuan</p>

                                @if($schedule->status->value === 'done')
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>

                            {{-- ALASAN PENOLAKAN --}}
                            @if($schedule->status->value === 'rejected')
                                <div>
                                    <p class="text-gray-500 mb-1">
                                        Alasan Penolakan dari Pengurus
                                    </p>

                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700">
                                        {{ $schedule->rejection_reason ?: 'Pengurus belum memberikan alasan penolakan.' }}
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="flex justify-end mt-6">
                            <button
                                type="button"
                                onclick="document.getElementById('detailModal-{{ $schedule->id }}').close()"
                                class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold">
                                Tutup
                            </button>
                        </div>
                    </div>
                </dialog>

                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Belum ada laporan kebersihan.
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
