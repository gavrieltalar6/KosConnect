@extends('layouts.app')
@section('title', 'Manajemen Penghuni')
@section('header', 'Daftar Penghuni')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded shadow">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h3 class="text-lg font-semibold">Semua Penghuni</h3>
        <a href="{{ route('pemilik.tenants.create') }}" class="w-full sm:w-auto text-center bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Tambah Penghuni</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamar</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $tenant->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $tenant->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $tenant->room ? 'Kamar ' . $tenant->room->room_number : 'Belum ada kamar' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $tenant->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tenant->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $tenant->status->getLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('password_resets.edit', $tenant->user_id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 rounded text-xs font-medium transition"
                                   title="Bantu reset password penghuni">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path>
                                    </svg>
                                    Reset Pass
                                </a>
                                <a href="{{ route('pemilik.tenants.show', $tenant) }}" 
                                   class="inline-flex items-center px-2.5 py-1 bg-gray-50 text-gray-700 hover:bg-gray-600 hover:text-white border border-gray-200 rounded text-xs font-medium transition">
                                    Detail
                                </a>
                                <a href="{{ route('pemilik.tenants.edit', $tenant) }}" 
                                   class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 rounded text-xs font-medium transition">
                                    Edit
                                </a>
                                @if(auth()->user()->role?->value === 'pemilik')
                                    <form action="{{ route('pemilik.tenants.destroy', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white border border-red-200 rounded text-xs font-medium transition" 
                                                onclick="return confirm('Yakin ingin menghapus?')">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data penghuni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
