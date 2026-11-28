@extends('layouts.app')
@section('title', 'Laporan Kebersihan')
@section('header', 'Laporan Kebersihan Kamar')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Laporan Kebersihan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Daftar pengajuan pembersihan kamar yang telah selesai atau ditolak.</p>
        </div>

        <a href="{{ route('penghuni.cleaning.index') }}"
           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            Lihat Jadwal Kebersihan &rarr;
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50/75">
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Tanggal Request
                    </th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Waktu
                    </th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Catatan
                    </th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($schedules as $schedule)
                    @php
                        $statusVal = is_string($schedule->status) ? $schedule->status : $schedule->status?->value;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $schedule->requested_date->translatedFormat('d M Y') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }} WIB
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs break-words">
                            {{ $schedule->note ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($statusVal === 'done')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <button type="button"
                                    onclick="openDetailModal({
                                        date: @js($schedule->requested_date->translatedFormat('d M Y')),
                                        time: @js(\Carbon\Carbon::parse($schedule->requested_time)->format('H:i') . ' WIB'),
                                        presence: @js($schedule->presence_status->value === 'occupied' ? 'Di Tempat' : 'Tidak di Tempat'),
                                        status: @js($statusVal),
                                        note: @js($schedule->note ?: 'Tidak ada catatan.'),
                                        rejectionReason: @js($schedule->rejection_reason ?: '')
                                    })"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 shadow-2xs hover:shadow-xs cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Belum ada laporan kebersihan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schedules->hasPages())
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    @endif
</div>

<!-- Modal Detail Laporan Kebersihan -->
<div id="cleaningDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="detailModalTitle" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Backdrop -->
        <div id="detailModalBackdrop" onclick="closeDetailModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity duration-300 ease-out opacity-0"></div>

        <!-- Modal Dialog Panel -->
        <div id="detailModalPanel" class="relative z-10 w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">
            <!-- Header -->
            <div class="bg-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 id="detailModalTitle" class="text-base font-bold text-white">Detail Laporan Kebersihan</h3>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <p id="detailDate" class="text-sm font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Waktu Pengajuan</span>
                        <p id="detailTime" class="text-sm font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Keberadaan Penghuni</span>
                        <p id="detailPresence" class="text-xs font-semibold text-gray-700 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</span>
                        <div class="mt-0.5">
                            <span id="detailStatusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Catatan Penghuni</span>
                    <div id="detailNote" class="mt-1.5 p-3.5 bg-gray-50 rounded-xl text-sm text-gray-700 leading-relaxed whitespace-pre-line border border-gray-100"></div>
                </div>

                <!-- Pesan Penolakan (muncul hanya jika ditolak) -->
                <div id="detailRejectionBox" class="hidden">
                    <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">Pesan Penolakan dari Pengurus</span>
                    <div class="mt-1.5 p-3.5 bg-red-50 border border-red-200 rounded-xl flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p id="detailRejectionReason" class="text-sm text-red-700 leading-relaxed whitespace-pre-line"></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3.5 flex justify-end border-t border-gray-100">
                <button type="button"
                        onclick="closeDetailModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const detailModal = document.getElementById('cleaningDetailModal');
    const detailBackdrop = document.getElementById('detailModalBackdrop');
    const detailPanel = document.getElementById('detailModalPanel');

    function openDetailModal(data) {
        document.getElementById('detailDate').textContent = data.date;
        document.getElementById('detailTime').textContent = data.time;
        document.getElementById('detailPresence').textContent = data.presence;
        document.getElementById('detailNote').textContent = data.note;

        const badge = document.getElementById('detailStatusBadge');
        const rejectionBox = document.getElementById('detailRejectionBox');
        const rejectionText = document.getElementById('detailRejectionReason');

        if (data.status === 'done') {
            badge.textContent = 'Selesai';
            badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800';
            rejectionBox.classList.add('hidden');
        } else {
            badge.textContent = 'Ditolak';
            badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800';
            rejectionText.textContent = data.rejectionReason ? data.rejectionReason : 'Pengurus belum memberikan alasan penolakan.';
            rejectionBox.classList.remove('hidden');
        }

        detailModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        detailBackdrop.classList.remove('opacity-100');
        detailBackdrop.classList.add('opacity-0');
        detailPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        detailPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                detailBackdrop.classList.remove('opacity-0');
                detailBackdrop.classList.add('opacity-100');
                detailPanel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                detailPanel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            });
        });
    }

    function closeDetailModal() {
        detailBackdrop.classList.remove('opacity-100');
        detailBackdrop.classList.add('opacity-0');
        detailPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        detailPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        setTimeout(() => {
            detailModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 280);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !detailModal.classList.contains('hidden')) {
            closeDetailModal();
        }
    });
</script>
@endsection
