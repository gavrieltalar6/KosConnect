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
                        onclick="document.getElementById('rejectModal-{{ $schedule->id }}').showModal()"
                        class="cleaning-action cleaning-action-reject"
                    >
                        Tolak
                    </button>

                </div>

                <!-- POPUP ALASAN PENOLAKAN -->
                <dialog
                    id="rejectModal-{{ $schedule->id }}"
                    class="reject-modal"
                >

                    <div class="reject-modal-content">

                        <div class="reject-modal-header">

                            <h3>Alasan Penolakan</h3>

                            <button
                                type="button"
                                class="reject-modal-close"
                                onclick="document.getElementById('rejectModal-{{ $schedule->id }}').close()"
                            >
                                &times;
                            </button>

                        </div>

                        <p class="reject-modal-description">
                            Silakan berikan alasan mengapa permintaan
                            pembersihan kamar ini ditolak. Alasan ini
                            nantinya dapat dilihat oleh penghuni.
                        </p>

                        <form
                            action="{{ route('pengurus.cleaning.update', $schedule) }}"
                            method="POST"
                        >
                            @csrf
                            @method('PUT')

                            <input
                                type="hidden"
                                name="status"
                                value="rejected"
                            >

                            <label for="rejection_reason_{{ $schedule->id }}">
                                Alasan Penolakan
                            </label>

                            <textarea
                                id="rejection_reason_{{ $schedule->id }}"
                                name="rejection_reason"
                                placeholder="Tuliskan alasan penolakan di sini..."
                                maxlength="1000"
                                required
                            ></textarea>

                            <div class="reject-modal-actions">

                                <button
                                    type="button"
                                    class="reject-modal-button reject-modal-cancel"
                                    onclick="document.getElementById('rejectModal-{{ $schedule->id }}').close()"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="reject-modal-button reject-modal-submit"
                                >
                                    Kirim Penolakan
                                </button>

                            </div>

                        </form>

                    </div>

                </dialog>

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

@endsection
