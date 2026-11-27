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
        grid-template-columns: repeat(3, minmax(0, 1fr));
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

    .stat-changed .cleaning-stat-number {
        color: #2563eb;
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

        <div class="cleaning-stat stat-changed">
            <div class="cleaning-stat-label">Diubah</div>
            <div class="cleaning-stat-number">
                {{ $stats['rescheduled'] }}
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

                        <button
                            type="submit"
                            class="cleaning-action cleaning-action-reject"
                        >
                            Tolak
                        </button>
                    </form>

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

@endsection
