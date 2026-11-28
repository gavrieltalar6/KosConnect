@extends('layouts.app')
@section('title', 'Pengingat Iuran')
@section('header', 'Pengingat Iuran Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Card Utama: Pengaturan & Status Terdekat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Pengingat Pembayaran Iuran Otomatis</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Aktifkan pengingat agar Anda mendapatkan notifikasi otomatis melalui Telegram menjelang dan saat hari jatuh tempo pembayaran kos.
                </p>
            </div>

            <!-- Saklar On-Off Penghuni -->
            <form action="{{ route('penghuni.reminders.toggle') }}" method="POST" class="flex items-center gap-3 bg-gray-50 px-4 py-2.5 rounded-xl border border-gray-200 shrink-0">
                @csrf
                @method('PATCH')
                <div class="text-right">
                    <div class="text-xs font-bold {{ $tenant->is_reminder_active ? 'text-emerald-700' : 'text-gray-500' }}">
                        {{ $tenant->is_reminder_active ? 'Pengingat Aktif' : 'Pengingat Nonaktif' }}
                    </div>
                    <div class="text-[10px] text-gray-400">
                        {{ $tenant->is_reminder_active ? 'Notifikasi otomatis menyala' : 'Notifikasi otomatis dimatikan' }}
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="is_reminder_active" 
                           value="1" 
                           class="sr-only peer"
                           onchange="this.form.submit()"
                           {{ $tenant->is_reminder_active ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </form>
        </div>

        <!-- Highlight Tagihan Terdekat & Status 3 Box -->
        <div class="bg-gradient-to-br from-indigo-50/70 to-blue-50/40 rounded-xl p-5 border border-indigo-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-indigo-600 text-white uppercase tracking-wider">
                        Tagihan Terdekat
                    </span>
                    @if($nearestInvoice)
                        <span class="text-xs font-semibold text-indigo-900">
                            Periode {{ $nearestInvoice->period }}
                        </span>
                    @endif
                </div>

                @if($nearestInvoice)
                    <div class="text-2xl font-black text-gray-900 mt-1">
                        Rp {{ number_format($nearestInvoice->amount, 0, ',', '.') }}
                    </div>

                    @php
                        $dueDate = $nearestInvoice->due_date;
                        $isToday = $dueDate->isToday();
                        $isPast = $dueDate->isPast() && !$isToday;
                        $diffDays = (int) ceil(now()->startOfDay()->diffInDays($dueDate->startOfDay(), false));
                    @endphp

                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Jatuh Tempo: <strong>{{ $dueDate->format('d M Y') }}</strong></span>
                        @if($isToday)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white animate-pulse">Hari ini!</span>
                        @elseif($isPast)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white">Terlambat {{ abs($diffDays) }} hari</span>
                        @else
                            <span class="text-xs text-indigo-700 font-semibold bg-indigo-100 px-2 py-0.5 rounded-md">({{ $diffDays }} hari lagi)</span>
                        @endif
                    </div>
                @else
                    <div class="text-base font-bold text-emerald-700 flex items-center gap-2 mt-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Semua tagihan Anda saat ini sudah lunas!
                    </div>
                    <p class="text-xs text-gray-500">Tidak ada tagihan yang menunggu pembayaran saat ini.</p>
                @endif
            </div>

            <!-- 3 Box Status Pesan Terkirim -->
            <div class="bg-white p-4 rounded-xl border border-indigo-100/80 shadow-2xs space-y-2 shrink-0">
                <div class="text-xs font-bold text-gray-700">Status Pesan Terkirim</div>
                <div class="flex items-center gap-2">
                    <!-- H-7 -->
                    <div class="px-3.5 py-2 rounded-lg text-xs font-bold text-center transition shadow-2xs
                        {{ $boxH7 === 'sent' ? 'bg-blue-600 text-white' : ($boxH7 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200') }}"
                         title="{{ $boxH7 === 'sent' ? 'Pesan H-7 Terkirim' : ($boxH7 === 'failed' ? 'Pesan H-7 Gagal Terkirim' : 'Pesan H-7 Belum Terkirim') }}">
                        H-7
                    </div>

                    <!-- H-1 -->
                    <div class="px-3.5 py-2 rounded-lg text-xs font-bold text-center transition shadow-2xs
                        {{ $boxH1 === 'sent' ? 'bg-blue-600 text-white' : ($boxH1 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200') }}"
                         title="{{ $boxH1 === 'sent' ? 'Pesan H-1 Terkirim' : ($boxH1 === 'failed' ? 'Pesan H-1 Gagal Terkirim' : 'Pesan H-1 Belum Terkirim') }}">
                        H-1
                    </div>

                    <!-- Hari H -->
                    <div class="px-3.5 py-2 rounded-lg text-xs font-bold text-center transition shadow-2xs
                        {{ $boxHDay === 'sent' ? 'bg-blue-600 text-white' : ($boxHDay === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200') }}"
                         title="{{ $boxHDay === 'sent' ? 'Pesan Hari H Terkirim' : ($boxHDay === 'failed' ? 'Pesan Hari H Gagal Terkirim' : 'Pesan Hari H Belum Terkirim') }}">
                        Hari H
                    </div>
                </div>

                <!-- Keterangan warna -->
                <div class="flex items-center justify-between text-[10px] text-gray-500 pt-1">
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-300"></span> Belum</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Terkirim</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Gagal</span>
                </div>
            </div>
        </div>

        <!-- Card Integrasi Akun Telegram -->
        <div class="p-5 rounded-xl border {{ $tenant->telegram_chat_id ? 'bg-blue-50/50 border-blue-200' : 'bg-amber-50/50 border-amber-200' }}">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl {{ $tenant->telegram_chat_id ? 'bg-blue-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.674.34l.205-3.05 5.566-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-gray-900">Integrasi Akun Telegram</h4>
                            @if($tenant->telegram_chat_id)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    Sudah Terhubung
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    Belum Terhubung
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            No. HP terdaftar: <span class="font-semibold text-gray-800">{{ $tenant->phone }}</span>
                            @if($tenant->telegram_chat_id)
                                • ID Chat Telegram: <span class="font-mono text-gray-700">{{ $tenant->telegram_chat_id }}</span>
                            @endif
                        </p>
                        @if(!$tenant->telegram_chat_id)
                            <p class="text-xs text-amber-800 mt-1.5">
                                Agar bot KosConnect dapat mengirimkan pengingat ke Telegram Anda, buka tautan bot di bawah dan tekan tombol <strong>Start</strong>.
                            </p>
                        @endif
                    </div>
                </div>

                @if($telegramLink && !$tenant->telegram_chat_id)
                    <div class="shrink-0">
                        <a href="{{ $telegramLink }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.34-.674.34l.205-3.05 5.566-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.894z"/></svg>
                            Hubungkan ke Bot Telegram
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Tagihan & Status Pengingat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Riwayat Tagihan & Status Pesan Pengingat</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar riwayat jadwal dan status pengiriman pengingat iuran Anda.</p>
            </div>
            <a href="{{ route('penghuni.invoices.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                Lihat Tagihan Saya &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50/75">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Periode Tagihan
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Tanggal Jatuh Tempo
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Pesan Terkirim
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status Pembayaran
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($invoices as $invoice)
                    @php
                        $paid = $invoice->status->value === 'paid';
                        $rowH7 = $invoice->getReminderBoxState('h_minus_7');
                        $rowH1 = $invoice->getReminderBoxState('h_minus_1');
                        $rowHDay = $invoice->getReminderBoxState('h_day');
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                        <!-- Periode & Jumlah -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900 text-sm">
                                {{ $invoice->period }}
                            </div>
                            <div class="text-xs text-gray-500 font-semibold mt-0.5">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- Tanggal Jatuh Tempo -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-800">
                                {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                Kamar {{ $invoice->room?->room_number ?? '-' }}
                            </div>
                        </td>

                        <!-- Pesan Terkirim (3 Box) -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="inline-flex items-center gap-2 p-1.5 bg-gray-50 rounded-xl border border-gray-200/80">
                                <!-- Box H-7 -->
                                <div class="px-2.5 py-1 rounded text-xs font-bold transition shadow-2xs text-center
                                    {{ $rowH7 === 'sent' ? 'bg-blue-600 text-white' : ($rowH7 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status H-7: {{ $rowH7 === 'sent' ? 'Terkirim' : ($rowH7 === 'failed' ? 'Gagal' : 'Belum') }}">
                                    H-7
                                </div>

                                <!-- Box H-1 -->
                                <div class="px-2.5 py-1 rounded text-xs font-bold transition shadow-2xs text-center
                                    {{ $rowH1 === 'sent' ? 'bg-blue-600 text-white' : ($rowH1 === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status H-1: {{ $rowH1 === 'sent' ? 'Terkirim' : ($rowH1 === 'failed' ? 'Gagal' : 'Belum') }}">
                                    H-1
                                </div>

                                <!-- Box Hari H -->
                                <div class="px-2.5 py-1 rounded text-xs font-bold transition shadow-2xs text-center
                                    {{ $rowHDay === 'sent' ? 'bg-blue-600 text-white' : ($rowHDay === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600') }}"
                                     title="Status Hari H: {{ $rowHDay === 'sent' ? 'Terkirim' : ($rowHDay === 'failed' ? 'Gagal' : 'Belum') }}">
                                    Hari H
                                </div>
                            </div>
                        </td>

                        <!-- Status Pembayaran -->
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($paid)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Belum Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada data riwayat tagihan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
