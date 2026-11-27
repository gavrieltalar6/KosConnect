@extends('layouts.app')
@section('title', 'Laporan Kerusakan')
@section('header', 'Laporan Kerusakan Fasilitas')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Laporan Kerusakan Anda</h3>
                <p class="text-sm text-gray-500 mt-1">Pantau perkembangan tindak lanjut kerusakan fasilitas kos yang telah Anda laporkan.</p>
            </div>
            <div>
                <a href="{{ route('penghuni.damage_reports.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Laporan Baru
                </a>
            </div>
        </div>

        <!-- Filter Status Tabs -->
        @php
            $currentStatus = request()->query('status');
        @endphp
        <div class="mt-6 pt-5 border-t border-gray-100 flex flex-wrap items-center gap-2">
            <a href="{{ route('penghuni.damage_reports.index') }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ empty($currentStatus) ? 'bg-indigo-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('penghuni.damage_reports.index', ['status' => 'open']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $currentStatus === 'open' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Menunggu
            </a>
            <a href="{{ route('penghuni.damage_reports.index', ['status' => 'in_progress']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $currentStatus === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                Sedang Diproses
            </a>
            <a href="{{ route('penghuni.damage_reports.index', ['status' => 'resolved']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $currentStatus === 'resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                Selesai
            </a>
            <a href="{{ route('penghuni.damage_reports.index', ['status' => 'cancelled']) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $currentStatus === 'cancelled' ? 'bg-gray-700 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Dibatalkan
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/75">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Laporan Kerusakan
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Lokasi
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Penanganan
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                        <!-- Judul & Deskripsi & Waktu -->
                        <td class="px-6 py-4">
                            <div class="max-w-md">
                                <div class="font-semibold text-gray-900 text-sm">
                                    {{ $report->title }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ $report->description }}
                                </p>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Diajukan: {{ $report->created_at ? $report->created_at->format('d M Y, H:i') : '-' }}
                                </div>
                            </div>
                        </td>

                        <!-- Lokasi -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->location_type === 'own_room')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    Kamar {{ $report->room->room_number ?? $report->room->number }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                    Ruang Umum
                                </span>
                            @endif
                        </td>

                        <!-- Penanganan / Pengurus -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->handler)
                                <div class="text-xs">
                                    <div class="font-medium text-gray-800 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                        {{ $report->handler->name }}
                                    </div>
                                    @if($report->resolved_at)
                                        <div class="text-[11px] text-emerald-600 mt-0.5">
                                            Selesai: {{ $report->resolved_at->format('d M Y, H:i') }}
                                        </div>
                                    @else
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Sedang diperiksa
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ditugaskan</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusVal = is_string($report->status) ? $report->status : $report->status?->value;
                            @endphp

                            @if($statusVal === 'open')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Diproses
                                </span>
                            @elseif($statusVal === 'in_progress')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Sedang Diproses
                                </span>
                            @elseif($statusVal === 'resolved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @elseif($statusVal === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ ucfirst($statusVal ?? '-') }}
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="inline-flex items-center gap-2">
                                <!-- Tombol Detail (MUNCUL UNTUK SEMUA STATUS) -->
                                <button type="button" 
                                        onclick="showDetailModal({{ json_encode([
                                            'title' => $report->title,
                                            'location' => $report->location_label,
                                            'room' => $report->location_label,
                                            'created_at' => $report->created_at ? $report->created_at->format('d M Y, H:i') : '-',
                                            'status' => $report->status instanceof \App\Enums\DamageReportStatus ? $report->status->getLabel() : ucfirst($statusVal ?? ''),
                                            'status_val' => $statusVal,
                                            'handler' => $report->handler->name ?? 'Belum ada',
                                            'resolved_at' => $report->resolved_at ? $report->resolved_at->format('d M Y, H:i') : '-',
                                            'description' => $report->description
                                        ]) }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 shadow-2xs hover:shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>

                                <!-- Tombol Batalkan (HANYA MUNCUL SAAT STATUS OPEN) -->
                                @if($statusVal === 'open')
                                    <form action="{{ route('penghuni.damage_reports.cancel', $report) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan laporan kerusakan ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 shadow-2xs hover:shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-500 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">Belum ada laporan kerusakan</h4>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                                    Jika ada fasilitas kamar kos yang rusak atau perlu perbaikan, ajukan laporan melalui tombol di bawah.
                                </p>
                                <a href="{{ route('penghuni.damage_reports.create') }}" 
                                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Ajukan Laporan Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Detail Laporan Penghuni -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div id="detailBackdrop" onclick="closeDetailModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity duration-300 ease-out opacity-0"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog Panel -->
        <div id="detailPanel" class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h3 class="text-base font-bold text-white">Detail Laporan Kerusakan</h3>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Judul Kerusakan</span>
                    <h4 id="modalTitle" class="text-base font-bold text-gray-900 mt-0.5"></h4>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3.5 rounded-xl border border-gray-100">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Lokasi</span>
                        <p id="modalRoom" class="text-sm font-semibold text-indigo-600 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Waktu Lapor</span>
                        <p id="modalCreatedAt" class="text-xs font-medium text-gray-700 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Pengurus Penanggung Jawab</span>
                        <p id="modalHandler" class="text-xs font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Status</span>
                        <p id="modalStatus" class="text-xs font-bold mt-0.5"></p>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Deskripsi Masalah</span>
                    <div id="modalDescription" class="mt-1.5 p-3.5 bg-gray-50 rounded-xl text-sm text-gray-700 leading-relaxed whitespace-pre-line border border-gray-100">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-3.5 flex justify-end border-t border-gray-100">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetailModal(data) {
    document.getElementById('modalTitle').textContent = data.title;
    document.getElementById('modalRoom').textContent = data.location ?? data.room;
    document.getElementById('modalCreatedAt').textContent = data.created_at;
    document.getElementById('modalHandler').textContent = data.handler;
    
    const statusElem = document.getElementById('modalStatus');
    statusElem.textContent = data.status;
    if (data.status_val === 'open') {
        statusElem.className = 'text-xs font-bold text-amber-600 mt-0.5';
    } else if (data.status_val === 'in_progress') {
        statusElem.className = 'text-xs font-bold text-blue-600 mt-0.5';
    } else if (data.status_val === 'resolved') {
        statusElem.className = 'text-xs font-bold text-emerald-600 mt-0.5';
    } else if (data.status_val === 'cancelled') {
        statusElem.className = 'text-xs font-bold text-red-600 mt-0.5';
    } else {
        statusElem.className = 'text-xs font-bold text-gray-600 mt-0.5';
    }

    document.getElementById('modalDescription').textContent = data.description;
    
    const modal = document.getElementById('detailModal');
    const backdrop = document.getElementById('detailBackdrop');
    const panel = document.getElementById('detailPanel');

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Reset initial animation classes
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
    panel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

    // Trigger smooth entrance
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            panel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        });
    });
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    const backdrop = document.getElementById('detailBackdrop');
    const panel = document.getElementById('detailPanel');

    // Smooth exit animation
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
    panel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 280);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endsection
