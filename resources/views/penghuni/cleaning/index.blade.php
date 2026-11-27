@extends('layouts.app')
@section('title', 'Jadwal Kebersihan')
@section('header', 'Riwayat Pengajuan Kebersihan')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded shadow">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h3 class="text-lg font-semibold">Riwayat Pengajuan</h3>
        <a href="{{ route('penghuni.cleaning.create') }}" class="w-full sm:w-auto text-center bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Ajukan Jadwal Baru</a>
    </div>

    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <tr>
                <td class="px-4 py-3 whitespace-nowrap">{{ $schedule->requested_date->format('d-m-Y') }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}</td>
                <td class="px-4 py-3">{{ $schedule->note ?? '-' }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ ucfirst($schedule->status->value) }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
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
</div>
@endsection
