@extends('layouts.app')
@section('title', 'Jadwal Kebersihan')
@section('header', 'Riwayat Pengajuan Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Riwayat Pengajuan</h3>
        <a href="{{ route('penghuni.cleaning.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Ajukan Jadwal Baru</a>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $schedule->requested_date->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}</td>
                <td>{{ $schedule->note ?? '-' }}</td>
                <td>{{ ucfirst($schedule->status->value) }}</td>
                <td>
                    @if($schedule->status->value === 'pending')
                        <a href="{{ route('penghuni.cleaning.edit', $schedule) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <form action="{{ route('penghuni.cleaning.destroy', $schedule) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin membatalkan jadwal ini?')">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
