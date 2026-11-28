@extends('layouts.app')
@section('title', 'Jadwal Kebersihan')
@section('header', 'Riwayat Pengajuan Kebersihan')

@section('content')
<div class="bg-white p-6 rounded shadow">

    {{-- NOTIFIKASI JIKA BELUM MEMILIKI KAMAR AKTIF --}}
    
@if(!$hasActiveRoom)
    <div class="mb-6 flex items-start gap-4 p-5"
         style="
            background-color: #ffffff;
            border: 1px solid #111111;
            border-radius: 16px;
         ">

        <div class="flex-shrink-0 rounded-lg bg-red-100 p-2">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-8 w-8 text-red-600"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 9v3m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.7 3h16.96a2 2 0 001.7-3L13.7 3.86a2 2 0 00-3.4 0z"/>
            </svg>
        </div>

        <div>
            <h3 class="font-semibold text-red-700">
                Anda belum terdaftar pada kamar aktif.
            </h3>

            <p class="mt-1 text-sm text-red-700">
                Harap hubungi pengurus atau pemilik kos sebelum membuat permintaan pembersihan kamar.
            </p>
        </div>
    </div>
@endif



    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Riwayat Pengajuan</h3>

        @if($hasActiveRoom)
            <a href="{{ route('penghuni.cleaning.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                Ajukan Jadwal Baru
            </a>
        @endif
    </div>

    {{-- PESAN SUKSES / ERROR --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-300 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 border border-red-300 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Waktu
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Catatan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $schedule->requested_date->format('d-m-Y') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($schedule->requested_time)->format('H:i') }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $schedule->note ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ ucfirst($schedule->status->value) }}
                        </td>

                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($schedule->status->value === 'pending')
                                <a href="{{ route('penghuni.cleaning.edit', $schedule) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-2">
                                    Edit
                                </a>

                                <form action="{{ route('penghuni.cleaning.destroy', $schedule) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Yakin ingin membatalkan jadwal ini?')">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada pengajuan jadwal kebersihan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($schedules, 'links'))
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    @endif

</div>
@endsection
