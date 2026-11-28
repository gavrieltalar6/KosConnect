
@extends('layouts.app')

@section('title', 'Dashboard Penghuni')
@section('header', 'Dashboard Penghuni')

@section('content')
<div class="space-y-6">

    {{-- Sambutan --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800">
            Selamat Datang!
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Berikut informasi tagihan kos yang masih perlu kamu selesaikan.
        </p>
    </div>

    {{-- Ringkasan Tagihan --}}
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
        <p class="text-sm font-medium text-indigo-600">
            Total Tagihan Belum Dibayar
        </p>

        <h3 class="mt-2 text-3xl font-bold text-indigo-800">
            {{ $unpaidInvoices->count() }}
        </h3>

        <p class="mt-1 text-sm text-indigo-500">
            Tagihan yang masih harus diselesaikan
        </p>
    </div>

    {{-- Daftar Tagihan --}}
    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">

        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="font-semibold text-gray-800">
                Tagihan Saya
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Daftar tagihan yang belum dibayar
            </p>
        </div>

        @forelse ($unpaidInvoices as $invoice)
            <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 last:border-0 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="font-semibold text-gray-800">
                        Tagihan Periode {{ $invoice->period }}
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Jatuh tempo:
                        {{ $invoice->due_date->format('d F Y') }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-700">
                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div>
                    @if ($invoice->due_date->lt(today()))
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            Terlambat
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            Belum Dibayar
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-7 w-7 text-green-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="mt-4 font-semibold text-gray-800">
                    Tidak Ada Tagihan
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Saat ini kamu tidak memiliki tagihan yang belum dibayar.
                </p>
            </div>
        @endforelse

    </div>
</div>
@endsection
