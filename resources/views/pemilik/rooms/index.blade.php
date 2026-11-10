@extends('layouts.app')
@section('title', 'Daftar Kamar')
@section('header', 'Manajemen Kamar')
@section('content')
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-semibold">Daftar Kamar</h2>
            <a href="{{ route('pemilik.rooms.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Tambah Kamar</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">No</th>
                    <th class="border px-4 py-2">Nomor Kamar</th>
                    <th class="border px-4 py-2">Lantai</th>
                    <th class="border px-4 py-2">Harga Bulanan</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr>
                        <td class="border px-4 py-2">{{ $loop->iteration + ($rooms->currentPage() - 1) * $rooms->perPage() }}</td>
                        <td class="border px-4 py-2">{{ $room->room_number }}</td>
                        <td class="border px-4 py-2">{{ $room->floor ?? '-' }}</td>
                        <td class="border px-4 py-2">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</td>
                        <td class="border px-4 py-2">
                            @php
                                $statusColors = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'occupied' => 'bg-blue-100 text-blue-800',
                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $statusText = [
                                    'available' => 'Tersedia',
                                    'occupied' => 'Dihuni',
                                    'maintenance' => 'Perbaikan',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-sm {{ $statusColors[$room->status->value] ?? '' }}">
                                {{ $statusText[$room->status->value] ?? $room->status->value }}
                            </span>
                        </td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('pemilik.rooms.edit', $room) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('pemilik.rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kamar ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $rooms->links() }}</div>
    </div>
@endsection
