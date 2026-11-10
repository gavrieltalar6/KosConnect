@extends('layouts.app')
@section('title', 'Verifikasi Kerusakan')
@section('header', 'Verifikasi Laporan Kerusakan')

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
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->tenant->full_name }}</td>
                <td>{{ $report->room->number }}</td>
                <td>{{ $report->title }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ ucfirst($report->status->value) }}</td>
                <td>
                    @if($report->status->value !== 'resolved')
                        <form action="{{ route('pengurus.damage_reports.update', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" style="background-color: #eab308 !important; color: white !important; padding: 4px 12px; border-radius: 4px; font-weight: bold; border: none; cursor: pointer;">Proses</button>
                        </form>
                        <form action="{{ route('pengurus.damage_reports.update', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="resolved">
                            <button type="submit" style="background-color: #22c55e !important; color: white !important; padding: 4px 12px; border-radius: 4px; font-weight: bold; border: none; cursor: pointer;">Selesai</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
