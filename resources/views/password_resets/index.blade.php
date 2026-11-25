@extends('layouts.app')
@section('title', 'Bantuan Reset Password')
@section('header', 'Bantuan Reset Password Pengguna')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-6 rounded shadow">
        <!-- Title di Tengah -->
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800 text-center">Daftar Akun Pengguna</h3>
            <p class="text-sm text-gray-500 text-center mt-1">Pilih akun staf pengurus atau penghuni yang ingin dibantu untuk reset kata sandi</p>
        </div>

        <!-- Form Pencarian & Filter di Tengah -->
        <div class="flex justify-center mb-6">
            <form action="{{ route('password_resets.index') }}" method="GET" class="flex flex-wrap items-center justify-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama atau email..." 
                    class="border border-gray-300 rounded px-3 py-1.5 text-sm w-56"
                >
                <select name="role" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1.5 text-sm cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="pengurus" {{ request('role') === 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                    <option value="penghuni" {{ request('role') === 'penghuni' ? 'selected' : '' }}>Penghuni</option>
                </select>
                <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1.5 text-sm cursor-pointer">
                    <option value="">Urutkan: Default</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                    <option value="role_pengurus" {{ request('sort') === 'role_pengurus' ? 'selected' : '' }}>Peran: Penghuni Dahulu</option>
                    <option value="role_penghuni" {{ request('sort') === 'role_penghuni' ? 'selected' : '' }}>Peran: Pengurus Dahulu</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded text-sm hover:bg-indigo-700">Filter</button>
                @if(request('search') || request('role') || request('sort'))
                    <a href="{{ route('password_resets.index') }}" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-sm hover:bg-gray-300">Reset</a>
                @endif
            </form>
        </div>

        <!-- Tabel Akun -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Email</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran (Role)</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamar / Detail</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akun</th>
                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->role->value === 'pengurus')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Pengurus
                                    </span>
                                @elseif($user->role->value === 'penghuni')
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Penghuni
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($user->role->value) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->tenant)
                                    {{ $user->tenant->room ? 'Kamar ' . $user->tenant->room->room_number : 'Belum ada kamar' }}
                                    <span class="text-xs text-gray-400 block">{{ $user->tenant->phone }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <!-- Tombol Aksi di Tengah -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('password_resets.edit', $user) }}" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold shadow-sm transition"
                                   style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 14px; background-color: #4f46e5; color: #ffffff; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path>
                                    </svg>
                                    <span>Reset Password</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                                Tidak ada akun yang cocok dengan pencarian atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
