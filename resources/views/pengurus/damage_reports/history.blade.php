@extends('layouts.app')
@section('title', 'Riwayat Kerusakan')
@section('header', 'Riwayat Laporan Kerusakan')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Riwayat Laporan Selesai</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar arsip laporan kerusakan fasilitas kos yang telah tuntas ditangani.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $reports->total() }} Laporan Selesai
                </span>
                <a href="{{ route('pengurus.damage_reports.index') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-lg transition-colors duration-150">
                    &larr; Kembali ke Laporan Aktif
                </a>
            </div>
        </div>

        <!-- Search Toolbar -->
        <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">Menampilkan seluruh data perbaikan yang telah diselesaikan.</span>
            <form action="{{ route('pengurus.damage_reports.history') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari penghuni, kamar, judul..." 
                           class="w-64 pl-9 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" width="16" height="16" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('pengurus.damage_reports.history') }}" class="px-2.5 py-1.5 text-xs text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/75">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Penghuni
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Lokasi
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Laporan Kerusakan
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Ditangani Oleh
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tanggal Selesai
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
                        <!-- Penghuni -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">
                                {{ $report->tenant->full_name ?? '-' }}
                            </div>
                            @if($report->tenant?->phone)
                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $report->tenant->phone }}
                                </div>
                            @endif
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

                        <!-- Judul & Deskripsi -->
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <div class="font-semibold text-gray-900 text-sm">
                                    {{ $report->title }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ $report->description }}
                                </p>
                            </div>
                        </td>

                        <!-- Ditangani Oleh -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($report->handler->name ?? 'P', 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-800">
                                    {{ $report->handler->name ?? '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Tanggal Selesai -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700 font-medium">
                                {{ $report->resolved_at ? $report->resolved_at->format('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $report->resolved_at ? $report->resolved_at->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Selesai
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button type="button" 
                                    onclick="showReportDetail({{ json_encode([
                                        'title' => $report->title,
                                        'tenant' => $report->tenant->full_name ?? '-',
                                        'phone' => $report->tenant?->phone ?? '-',
                                        'location' => $report->location_label,
                                        'room' => $report->location_label,
                                        'handler' => $report->handler->name ?? '-',
                                        'created_at' => $report->created_at ? $report->created_at->format('d M Y, H:i') : '-',
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-base font-semibold text-gray-800">
                                    {{ !empty($search) ? 'Tidak ada riwayat laporan yang cocok' : 'Belum ada riwayat laporan' }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm">
                                    {{ !empty($search) ? 'Coba periksa kembali kata kunci pencarian Anda.' : 'Laporan kerusakan yang telah diselesaikan akan tercatat secara otomatis di halaman ini.' }}
                                </p>
                                @if(!empty($search))
                                    <a href="{{ route('pengurus.damage_reports.history') }}" class="mt-3 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                        Hapus pencarian
                                    </a>
                                @endif
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

<!-- Modal Detail Laporan -->
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-bold text-white">Detail Riwayat Laporan Kerusakan</h3>
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
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Pelapor</span>
                        <p id="modalTenant" class="text-sm font-semibold text-gray-800 mt-0.5"></p>
                        <p id="modalPhone" class="text-xs text-gray-500"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Lokasi</span>
                        <p id="modalLocation" class="text-sm font-semibold text-indigo-600 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Ditangani Oleh</span>
                        <p id="modalHandler" class="text-xs font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-gray-400 uppercase">Tanggal Selesai</span>
                        <p id="modalResolvedAt" class="text-xs font-bold text-emerald-600 mt-0.5"></p>
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
function showReportDetail(data) {
    document.getElementById('modalTitle').textContent = data.title;
    document.getElementById('modalTenant').textContent = data.tenant;
    document.getElementById('modalPhone').textContent = data.phone !== '-' ? data.phone : '';
    document.getElementById('modalLocation').textContent = data.location ?? data.room;
    document.getElementById('modalHandler').textContent = data.handler;
    document.getElementById('modalResolvedAt').textContent = data.resolved_at;
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
