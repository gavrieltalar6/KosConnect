@extends('layouts.app')
@section('title', 'Verifikasi Kebersihan')
@section('header', 'Verifikasi Jadwal Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Daftar Tunggu Verifikasi</h3>
        <a href="{{ route('pengurus.cleaning.report') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Lihat Laporan Selesai &rarr;</a>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th>Penghuni</th>
                <th>Tanggal Diminta</th>
                <th>Waktu</th>
                <th>Status Keberadaan</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $schedule->tenant->full_name }}</td>
                <td>{{ $schedule->requested_date->format('d-m-Y') }}</td>
                <td>{{ $schedule->requested_time }}</td>
                <td>{{ ucfirst($schedule->presence_status->value) }}</td>
                <td>{{ $schedule->note }}</td>
                <td>{{ ucfirst($schedule->status->value) }}</td>
                <td>
                    @if($schedule->status->value === 'pending')
                        <form action="{{ route('pengurus.cleaning.update', $schedule) }}" method="POST" class="inline-block mr-1">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" style="background-color: #22c55e; color: #000000; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; border: 1px solid #166534;">Setujui</button>
                        </form>
                        <form action="{{ route('pengurus.cleaning.update', $schedule) }}" method="POST" class="inline-block">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" style="background-color: #ef4444; color: #000000; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; border: 1px solid #991b1b;">Tolak</button>
                        </form>
                    @elseif($schedule->status->value === 'approved')
                        <form action="{{ route('pengurus.cleaning.markAsDone', $schedule) }}" method="POST" class="inline-block">
                            @csrf
                            @method('POST')
                            <button type="submit" style="background-color: #3b82f6; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; border: 1px solid #1e40af;">Selesai</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
