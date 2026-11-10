@extends('layouts.app')
@section('title', 'Laporan Kerusakan')
@section('header', 'Laporan Kerusakan Fasilitas')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Riwayat Laporan</h3>
        <a href="{{ route('penghuni.damage_reports.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Buat Laporan Baru</a>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->title }}</td>
                <td>
                    @if($report->status === \App\Enums\DamageReportStatus::CANCELLED)
                        <span class="inline-flex items-center px-3 py-2 rounded text-sm font-medium bg-red-100 text-red-800">
                            {{ ucfirst($report->status->value) }}
                        </span>
                    @else
                        {{ ucfirst($report->status->value) }}
                    @endif
                </td>
                <td>{{ $report->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    @if($report->status === \App\Enums\DamageReportStatus::OPEN)
                        <div class="flex gap-2">
                            <!-- Detail Button -->
                            <button onclick="showDetails('{{ $report->title }}', '{{ $report->description }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">Detail</button>

                            <!-- Cancel Button -->
                            <form action="{{ route('penghuni.damage_reports.cancel', $report) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan laporan ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background-color: #dc2626 !important; color: white !important;" class="hover:bg-red-800 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105 border border-red-700">Batalkan</button>
                            </form>
                        </div>
                    @else
                        @if($report->status === \App\Enums\DamageReportStatus::CANCELLED)
                            <button onclick="showDetails('{{ $report->title }}', '{{ $report->description }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">Detail</button>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-auto transform transition-all">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-semibold text-gray-800">Detail Laporan Kerusakan</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-700 mb-2">📋 Judul:</h4>
                    <p id="modalTitle" class="text-gray-800 bg-gray-50 p-3 rounded-lg font-medium"></p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">📝 Deskripsi:</h4>
                    <p id="modalDescription" class="text-gray-800 bg-gray-50 p-3 rounded-lg whitespace-pre-wrap"></p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button onclick="closeModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function showDetails(title, description) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDescription').textContent = description;
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('detailsModal').classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</div>
@endsection
