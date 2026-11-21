@extends('layouts.app')
@section('title', 'Riwayat Kerusakan')
@section('header', 'Riwayat Laporan Kerusakan')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Ditangani Oleh</th>
                <th>Tanggal Selesai</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($reports as $report)
            <tr>
                <td>{{ $report->tenant->full_name ?? '-' }}</td>
                <td>{{ $report->room->number ?? '-' }}</td>
                <td>{{ $report->title }}</td>
                <td>{{ $report->description }}</td>
                <td>
                    <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px;">
                        Selesai
                    </span>
                </td>
                <td>{{ $report->handler->name ?? '-' }}</td>
                <td>
                    {{ $report->resolved_at ? $report->resolved_at->format('d-m-Y H:i') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    Belum ada riwayat laporan kerusakan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection
