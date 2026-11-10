@extends('layouts.app')
@section('title', 'Detail Penghuni')
@section('header', 'Detail Penghuni: ' . $tenant->full_name)

@section('content')
<div class="bg-white p-6 rounded shadow max-w-2xl mx-auto">
    <div class="mb-4 border-b pb-4">
        <h3 class="text-lg font-semibold text-gray-800">Informasi Akun</h3>
        <p><strong>Email:</strong> {{ $tenant->user->email }}</p>
        <p><strong>Status Akun:</strong> {{ $tenant->user->is_active ? 'Aktif' : 'Nonaktif' }}</p>
    </div>

    <div class="mb-4 border-b pb-4">
        <h3 class="text-lg font-semibold text-gray-800">Informasi Pribadi</h3>
        <p><strong>Nama Lengkap:</strong> {{ $tenant->full_name }}</p>
        <p><strong>No HP:</strong> {{ $tenant->phone }}</p>
        <p><strong>Alamat Asal:</strong> {{ $tenant->origin_address }}</p>
        <p><strong>No HP Orang Tua/Wali:</strong> {{ $tenant->guardian_phone }}</p>
    </div>

    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Informasi Kos</h3>
        <p><strong>Kamar:</strong> {{ $tenant->room ? 'Kamar ' . $tenant->room->room_number : 'Belum ada kamar' }}</p>
        <p><strong>Tanggal Check-In:</strong> {{ $tenant->check_in_date->format('d-m-Y') }}</p>
        <p><strong>Tanggal Jatuh Tempo:</strong> Setiap tanggal {{ $tenant->due_day }}</p>
        <p><strong>Status Penghuni:</strong> {{ ucfirst($tenant->status->value) }}</p>
    </div>

    <div class="flex justify-end gap-2 mt-6">
        <a href="{{ route('pemilik.tenants.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
        <a href="{{ route('pemilik.tenants.edit', $tenant) }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Edit</a>
    </div>
</div>
@endsection
