@extends('layouts.app')
@section('title', 'Laporan Kebersihan')
@section('header', 'Laporan Seluruh Kebersihan Kamar')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Log Laporan Kebersihan</h3>
        <a href="{{ route('pengurus.cleaning.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Lihat Verifikasi Jadwal &rarr;</a>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penghuni</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($schedules as $schedule)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->tenant->full_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->room->room_number ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->requested_date->format('d-m-Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($schedule->status->value === 'done')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Done</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $schedule->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada laporan kebersihan yang selesai.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $schedules->links() }}
    </div>
</div>
@endsection
