@extends('layouts.app')

@section('title', 'Manajemen Pembersihan Kamar')
@section('header', 'Manajemen Pembersihan Kamar')

@section('content')

<style>
    .cleaning-page {
        margin: -6px;
        padding: 18px;
        min-height: calc(100vh - 90px);
        background: #f7f9fc;
        color: #111827;
        font-family: Arial, Helvetica, sans-serif;
    }

    .cleaning-page * {
        box-sizing: border-box;
    }

    /* JUDUL */
    .cleaning-heading {
        margin: 0 0 18px;
    }

    .cleaning-heading h1 {
        margin: 0;
        font-size: 25px;
        line-height: 1.25;
        font-weight: 600;
        color: #111827;
    }

    .cleaning-heading p {
        margin: 16px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.4;
    }

    /* STATISTIK */
    .cleaning-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 15px;
    }

    .cleaning-stat {
        min-height: 90px;
        padding: 12px 11px;
        border-radius: 16px;
        background: #fff;
    }

    .cleaning-stat-label {
        color: #64748b;
        font-size: 14px;
    }

    .cleaning-stat-number {
        margin-top: 15px;
        font-size: 29px;
        line-height: 1;
        font-weight: 500;
    }

    .cleaning-room {
    margin-top: 4px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    }

    .stat-pending .cleaning-stat-number {
        color: #d58a00;
    }

    .stat-approved .cleaning-stat-number {
        color: #16a34a;
    }

    /* SISTEM ANTREAN */
    .cleaning-info {
        margin-bottom: 15px;
        padding: 18px 16px 15px;
        border-radius: 16px;
        background: #eef2ff;
        color: #3730a3;
    }

    .cleaning-info-title {
        margin: 0 0 3px;
        font-size: 16px;
        font-weight: 600;
    }

    .cleaning-info p {
        margin: 0;
        font-size: 14px;
        color: #4338ca;
    }

    /* DAFTAR PERMINTAAN */
    .cleaning-list {
        padding: 20px 15px 15px;
        border-radius: 16px;
        background: #fff;
    }

    .cleaning-list h2 {
        margin: 0;
        padding-bottom: 24px;
        border-bottom: 1px solid #dbe3ee;
        font-size: 19px;
        line-height: 1.35;
        font-weight: 600;
    }

    .cleaning-request {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 22px 0 0;
    }

    .cleaning-request-main {
        min-width: 0;
    }

    .cleaning-muted {
        color: #64748b;
        font-size: 14px;
    }

    .cleaning-tenant {
        margin-top: 7px;
        font-size: 17px;
        font-weight: 600;
        color: #111827;
    }

    .cleaning-time {
        margin-top: 13px;
    }

    /* STATUS */
    .cleaning-badge {
        flex: 0 0 auto;
        align-self: center;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 14px;
        line-height: 1.2;
        white-space: nowrap;
    }

    .badge-pending {
        color: #9a3412;
        background: #ffeadf;
    }

    .badge-approved {
        color: #166534;
        background: #dcfce7;
    }

    .badge-rescheduled {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .badge-rejected {
        color: #991b1b;
        background: #fee2e2;
    }

    /* TOMBOL */
    .cleaning-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 10px;
    }

    .cleaning-action {
        display: block;
        width: 100%;
        min-height: 40px;
        border: 0;
        border-radius: 11px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .cleaning-action-approve {
        background: #16a34a;
    }

    .cleaning-action-reject {
        background: #e51f26;
    }

    .cleaning-action-done {
        background: #2563eb;
    }

    .cleaning-action:hover {
        filter: brightness(0.94);
    }

    .cleaning-empty {
        padding: 24px 0 8px;
        color: #64748b;
        text-align: center;
        font-size: 14px;
    }

    .cleaning-pagination {
        margin-top: 18px;
    }

    .cleaning-report-link {
        display: inline-block;
        margin-top: 14px;
        color: #4f46e5;
        font-size: 14px;
        text-decoration: none;
    }

    /* POPUP PENOLAKAN */
    .reject-modal {
        width: calc(100% - 32px);
        max-width: 450px;
        padding: 0;
        border: none;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    .reject-modal::backdrop {
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(2px);
    }

    .reject-modal-content {
        padding: 24px;
    }

    .reject-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .reject-modal-header h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #111827;
    }

    .reject-modal-close {
        border: none;
        background: transparent;
        color: #94a3b8;
        font-size: 26px;
        cursor: pointer;
    }

    .reject-modal-description {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    .reject-modal label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 14px;
        font-weight: 600;
    }

    .reject-modal textarea {
        display: block;
        width: 100%;
        min-height: 115px;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 11px;
        outline: none;
        resize: vertical;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    .reject-modal textarea:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .reject-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .reject-modal-button {
        min-height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .reject-modal-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .reject-modal-submit {
        background: #e51f26;
        color: #fff;
    }

    .reject-modal-submit:hover {
        background: #c91c22;
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
        .cleaning-page {
            padding: 14px;
        }

        .cleaning-heading h1 {
            font-size: 22px;
        }

        .cleaning-stats {
            gap: 8px;
        }

        .cleaning-stat {
            min-height: 82px;
            padding: 10px 9px;
        }

        .cleaning-stat-label {
            font-size: 12px;
        }

        .cleaning-stat-number {
            font-size: 25px;
        }

        .cleaning-list {
            padding: 17px 13px 13px;
        }

        .cleaning-request {
            align-items: flex-start;
        }

        .cleaning-badge {
            font-size: 12px;
        }

        .cleaning-tenant {
            font-size: 15px;
        }
    }

    @media (max-width: 380px) {
        .cleaning-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="cleaning-page">

    <!-- JUDUL -->
    <div class="cleaning-heading">
        <h1>Manajemen Pembersihan Kamar</h1>
        <p>Kelola dan verifikasi permintaan penghuni.</p>
    </div>

    <!-- STATISTIK -->
    <div class="cleaning-stats">

        <div class="cleaning-stat stat-pending">
            <div class="cleaning-stat-label">Menunggu</div>
            <div class="cleaning-stat-number">
                {{ $stats['pending'] }}
            </div>
        </div>

        <div class="cleaning-stat stat-approved">
            <div class="cleaning-stat-label">Disetujui</div>
            <div class="cleaning-stat-number">
                {{ $stats['approved'] }}
            </div>
        </div>

    </div>

    <!-- SISTEM ANTREAN -->
    <div class="cleaning-info">
        <div class="cleaning-info-title">
            ⓘ Sistem Antrean
        </div>

        <p>
            Satu jadwal hanya dapat disetujui untuk satu permintaan.
        </p>
    </div>

    <!-- DAFTAR PERMINTAAN -->
    <div class="cleaning-list">

        <h2>Daftar Permintaan Pembersihan</h2>

        @forelse($schedules as $schedule)

            @php
                $statusValue = $schedule->status->value;

                $statusLabel = match ($statusValue) {
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rescheduled' => 'Diubah',
                    'rejected' => 'Ditolak',
                    'done' => 'Selesai',
                    default => ucfirst($statusValue),
                };

                $badgeClass = match ($statusValue) {
                    'pending' => 'badge-pending',
                    'approved' => 'badge-approved',
                    'rescheduled' => 'badge-rescheduled',
                    'rejected' => 'badge-rejected',
                    default => '',
                };
            @endphp

            <div class="cleaning-request">

                <div class="cleaning-request-main">

                    <div class="cleaning-muted">
                        Penghuni
                    </div>

                    <div class="cleaning-tenant">
                        {{ $schedule->tenant->full_name ?? 'Penghuni tidak ditemukan' }}
                    </div>

                    <div class="cleaning-room">
                        Kamar {{ $schedule->room?->room_number ?? $schedule->tenant?->room?->room_number ?? '-' }}
                    </div>

                    <div class="cleaning-muted cleaning-time">

                        {{ $schedule->requested_date->translatedFormat('d M Y') }}

                        &bull;

                        {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}

                        &bull;

                        {{ $schedule->presence_status->value === 'occupied'
                            ? 'Di Tempat'
                            : 'Tidak di Tempat' }}

                    </div>

                </div>

                <span class="cleaning-badge {{ $badgeClass }}">
                    {{ $statusLabel }}
                </span>

            </div>

            <!-- TOMBOL SETUJUI DAN TOLAK -->
            @if($statusValue === 'pending')

                <div class="cleaning-actions">

                    <!-- TOMBOL SETUJUI -->
                    <form
                        action="{{ route('pengurus.cleaning.update', $schedule) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="status"
                            value="approved"
                        >

                        <button
                            type="submit"
                            class="cleaning-action cleaning-action-approve"
                        >
                            Setujui
                        </button>
                    </form>

                    <!-- TOMBOL BUKA POPUP PENOLAKAN -->
                    <button
                        type="button"
                        onclick="openRejectModal({
                            actionUrl: @js(route('pengurus.cleaning.update', $schedule)),
                            tenantName: @js($schedule->tenant->full_name ?? 'Penghuni'),
                            roomLabel: @js('Kamar ' . ($schedule->room?->room_number ?? $schedule->tenant?->room?->room_number ?? '-')),
                            scheduleTime: @js($schedule->requested_date->translatedFormat('d M Y') . ' • ' . \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') . ' WIB')
                        })"
                        class="cleaning-action cleaning-action-reject"
                    >
                        Tolak
                    </button>

                </div>

            @elseif($statusValue === 'approved')

                <div class="cleaning-actions">

                    <form
                        action="{{ route('pengurus.cleaning.markAsDone', $schedule) }}"
                        method="POST"
                        style="grid-column: 1 / -1;"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="cleaning-action cleaning-action-done"
                        >
                            Tandai Selesai
                        </button>
                    </form>

                </div>

            @endif

        @empty

            <div class="cleaning-empty">
                Belum ada permintaan pembersihan kamar.
            </div>

        @endforelse

        <!-- PAGINATION -->
        <div class="cleaning-pagination">
            {{ $schedules->links() }}
        </div>

        <a
            href="{{ route('pengurus.cleaning.report') }}"
            class="cleaning-report-link"
        >
            Lihat Laporan Selesai &rarr;
        </a>

    </div>

</div>

<!-- Modal Pesan Penolakan Pembersihan -->
<div id="rejectCleaningModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="rejectModalTitle" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Backdrop -->
        <div id="rejectModalBackdrop" onclick="closeRejectModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity duration-300 ease-out opacity-0"></div>

        <!-- Modal Dialog Panel -->
        <div id="rejectModalPanel" class="relative z-10 w-full max-w-md bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">
            <!-- Header -->
            <div class="bg-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 id="rejectModalTitle" class="text-base font-bold text-white">Tolak Permintaan Kebersihan</h3>
                </div>
                <button type="button" onclick="closeRejectModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="rejectCleaningForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Pengajuan Penghuni</span>
                            <p id="rejectModalTenant" class="text-sm font-bold text-gray-900 truncate mt-0.5"></p>
                            <p id="rejectModalSchedule" class="text-xs text-gray-500 mt-0.5"></p>
                        </div>
                        <span id="rejectModalRoom" class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100"></span>
                    </div>

                    <div>
                        <label for="rejectReasonInput" class="block text-sm font-semibold text-gray-700 mb-1">
                            Pesan / Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">
                            Pesan ini akan dikirimkan dan dapat dibaca langsung oleh penghuni pada halaman Laporan Kebersihan.
                        </p>
                        <textarea
                            id="rejectReasonInput"
                            name="rejection_reason"
                            rows="4"
                            maxlength="1000"
                            required
                            placeholder="Tuliskan alasan penolakan dengan jelas..."
                            class="block w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-3.5 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button type="button"
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transform transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const rejectModal = document.getElementById('rejectCleaningModal');
    const rejectBackdrop = document.getElementById('rejectModalBackdrop');
    const rejectPanel = document.getElementById('rejectModalPanel');
    const rejectForm = document.getElementById('rejectCleaningForm');
    const rejectReasonInput = document.getElementById('rejectReasonInput');

    function openRejectModal(data) {
        rejectForm.action = data.actionUrl;
        document.getElementById('rejectModalTenant').textContent = data.tenantName;
        document.getElementById('rejectModalRoom').textContent = data.roomLabel;
        document.getElementById('rejectModalSchedule').textContent = data.scheduleTime;
        rejectReasonInput.value = '';

        rejectModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        rejectBackdrop.classList.remove('opacity-100');
        rejectBackdrop.classList.add('opacity-0');
        rejectPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        rejectPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                rejectBackdrop.classList.remove('opacity-0');
                rejectBackdrop.classList.add('opacity-100');
                rejectPanel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                rejectPanel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                setTimeout(() => rejectReasonInput.focus(), 150);
            });
        });
    }

    function closeRejectModal() {
        rejectBackdrop.classList.remove('opacity-100');
        rejectBackdrop.classList.add('opacity-0');
        rejectPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        rejectPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        setTimeout(() => {
            rejectModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 280);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !rejectModal.classList.contains('hidden')) {
            closeRejectModal();
        }
    });
</script>

@endsection
