@extends('layouts.app')
@section('title', 'Tagihan Saya')
@section('header', 'Tagihan Saya')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded shadow flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Total yang harus dibayar</p>
            <p class="text-3xl font-bold {{ $totalUnpaid > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
        </div>
        @if($totalUnpaid == 0)
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold">Tidak ada tunggakan</span>
        @endif
    </div>

    <div id="pay-alert" class="hidden px-4 py-3 rounded-xl text-sm font-medium"></div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Daftar Tagihan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        @foreach(['Periode', 'Kamar', 'Jumlah', 'Jatuh Tempo', 'Status', 'Aksi'] as $h)
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        @php
                            $paid = $invoice->status->value === 'paid';
                            $overdue = $invoice->isOverdue();
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->period }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->room?->room_number ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->due_date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paid)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                    <div class="text-xs text-gray-500 mt-1">{{ $invoice->paid_at?->format('d-m-Y') }}</div>
                                @elseif($overdue)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @unless($paid)
                                    <button type="button" data-pay="{{ route('penghuni.invoices.snap-token', $invoice) }}"
                                        class="pay-btn bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Bayar Sekarang
                                    </button>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const alertBox = document.getElementById('pay-alert');

    function showAlert(msg, type) {
        alertBox.className = 'px-4 py-3 rounded-xl text-sm font-medium border ' +
            (type === 'ok' ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
             : type === 'info' ? 'bg-yellow-50 border-yellow-200 text-yellow-800'
             : 'bg-red-50 border-red-200 text-red-800');
        alertBox.textContent = msg;
    }

    // Status tagihan baru berubah setelah Midtrans mengirim notifikasi ke server,
    // jadi setelah sukses kita muat ulang sebentar lagi.
    function reloadSoon() { setTimeout(() => window.location.reload(), 2500); }

    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (typeof window.snap === 'undefined') {
                showAlert('Layanan pembayaran belum siap. Periksa koneksi atau Client Key Midtrans.', 'err');
                return;
            }
            btn.disabled = true;
            try {
                const res = await fetch(btn.dataset.pay, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memulai pembayaran.');

                window.snap.pay(data.token, {
                    onSuccess: () => { showAlert('Pembayaran berhasil. Memperbarui status…', 'ok'); reloadSoon(); },
                    onPending: () => { showAlert('Menunggu pembayaran Anda. Selesaikan sesuai instruksi yang ditampilkan.', 'info'); btn.disabled = false; },
                    onError:   () => { showAlert('Pembayaran gagal. Silakan coba lagi.', 'err'); btn.disabled = false; },
                    onClose:   () => { btn.disabled = false; },
                });
            } catch (e) {
                showAlert(e.message, 'err');
                btn.disabled = false;
            }
        });
    });
</script>
@endsection
