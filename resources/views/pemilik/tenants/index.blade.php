@extends('layouts.app')
@section('title', 'Manajemen Penghuni')
@section('header', 'Daftar Penghuni')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Semua Penghuni</h3>
        <a href="{{ route('pemilik.tenants.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Tambah Penghuni</a>
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
                                <button type="button"
                                        onclick="openEditModal({
                                            name: @js($tenant->full_name),
                                            email: @js($tenant->user->email ?? '-'),
                                            isActive: {{ $tenant->status->value === 'active' ? 'true' : 'false' }},
                                            editUrl: @js(route('pemilik.tenants.edit', $tenant)),
                                            deactivateUrl: @js(route('pemilik.tenants.deactivate', $tenant)),
                                            activateUrl: @js(route('pemilik.tenants.activate', $tenant)),
                                            destroyUrl: @js(route('pemilik.tenants.destroy', $tenant))
                                        })"
                                        class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 rounded text-xs font-medium transition cursor-pointer">
                                    Edit
                                </button>
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

<!-- Popup Modal Edit Penghuni -->
<div id="editTenantModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="editModalTitle" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Backdrop -->
        <div id="editModalBackdrop" onclick="closeEditModal()" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity duration-300 ease-out opacity-0"></div>

        <!-- Modal Dialog Panel -->
        <div id="editModalPanel" class="relative z-10 w-full max-w-md bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4">
            <!-- Header -->
            <div class="bg-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h3 id="editModalTitle" class="text-base font-bold text-white">Kelola Data Penghuni</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Penghuni</span>
                        <p id="editModalName" class="text-sm font-bold text-gray-900 truncate mt-0.5"></p>
                        <p id="editModalEmail" class="text-xs text-gray-500 truncate"></p>
                    </div>
                    <span id="editModalStatusBadge" class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full"></span>
                </div>

                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pilih Tindakan</span>
                    <div class="mt-2 space-y-2.5">
                        <!-- Form Nonaktifkan / Aktifkan -->
                        <form id="toggleStatusForm" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    id="toggleStatusBtn"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm shadow-xs transform transition-all duration-150 hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                            </button>
                        </form>

                        <!-- Tombol Edit Detail -->
                        <a id="editDetailLink"
                           href="#"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 shadow-2xs transform transition-all duration-150 hover:scale-[1.01] active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Detail
                        </a>

                        @if(auth()->user()->role?->value === 'pemilik')
                            <!-- Form Hapus -->
                            <form id="destroyTenantForm" method="POST" onsubmit="return confirm('Yakin ingin menghapus data penghuni ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm bg-red-50 text-red-700 hover:bg-red-600 hover:text-white border border-red-200 shadow-2xs transform transition-all duration-150 hover:scale-[1.01] active:scale-[0.99] cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3.5 flex justify-end border-t border-gray-100">
                <button type="button"
                        onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold rounded-lg transform transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const editModal = document.getElementById('editTenantModal');
    const editBackdrop = document.getElementById('editModalBackdrop');
    const editPanel = document.getElementById('editModalPanel');
    const editModalName = document.getElementById('editModalName');
    const editModalEmail = document.getElementById('editModalEmail');
    const editModalStatusBadge = document.getElementById('editModalStatusBadge');
    const toggleStatusForm = document.getElementById('toggleStatusForm');
    const toggleStatusBtn = document.getElementById('toggleStatusBtn');
    const editDetailLink = document.getElementById('editDetailLink');
    const destroyTenantForm = document.getElementById('destroyTenantForm');

    function openEditModal(data) {
        editModalName.textContent = data.name;
        editModalEmail.textContent = data.email;
        editDetailLink.href = data.editUrl;
        if (destroyTenantForm) {
            destroyTenantForm.action = data.destroyUrl;
        }

        if (data.isActive) {
            editModalStatusBadge.textContent = 'Aktif';
            editModalStatusBadge.className = 'shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800';

            toggleStatusForm.action = data.deactivateUrl;
            toggleStatusBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Nonaktifkan
            `;
            toggleStatusBtn.className = 'w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white border border-amber-200 shadow-2xs transform transition-all duration-150 hover:scale-[1.01] active:scale-[0.99] cursor-pointer';
        } else {
            editModalStatusBadge.textContent = 'Nonaktif';
            editModalStatusBadge.className = 'shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';

            toggleStatusForm.action = data.activateUrl;
            toggleStatusBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Aktifkan
            `;
            toggleStatusBtn.className = 'w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 shadow-2xs transform transition-all duration-150 hover:scale-[1.01] active:scale-[0.99] cursor-pointer';
        }

        editModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        editBackdrop.classList.remove('opacity-100');
        editBackdrop.classList.add('opacity-0');
        editPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        editPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                editBackdrop.classList.remove('opacity-0');
                editBackdrop.classList.add('opacity-100');
                editPanel.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                editPanel.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            });
        });
    }

    function closeEditModal() {
        editBackdrop.classList.remove('opacity-100');
        editBackdrop.classList.add('opacity-0');
        editPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        editPanel.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        setTimeout(() => {
            editModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 280);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
            closeEditModal();
        }
    });
</script>
@endsection
