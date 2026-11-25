<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KosConnect') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-700 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-indigo-600">
                {{ config('app.name') }}
            </div>
            <nav class="flex-grow p-4 space-y-2">
                @php
                    $role = auth()->user()->role?->value;
                @endphp

                <a href="{{ route($role . '.dashboard') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs($role . '.dashboard') ? 'bg-indigo-600' : '' }}">
                    Dashboard
                </a>

                @if($role === 'pemilik')
                    <a href="{{ route('pemilik.tenants.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.tenants.*') ? 'bg-indigo-600' : '' }}">Manajemen Penghuni</a>
                    <a href="{{ route('pemilik.rooms.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.rooms.*') ? 'bg-indigo-600' : '' }}">
                        Manajemen Kamar
                    </a>
                    <a href="{{ route('pemilik.invoices.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.invoices.*') ? 'bg-indigo-600' : '' }}">
                        Manajemen Tagihan
                    </a>
                    <a href="{{ route('password_resets.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('password_resets.*') ? 'bg-indigo-600' : '' }}">
                        Bantuan Reset Password
                    <a href="{{ route('pemilik.damage_reports.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.damage_reports.*') ? 'bg-indigo-600' : '' }}">
                        Monitoring Kerusakan
                    </a>
                @endif

                @if($role === 'pengurus')
                    <a href="{{ route('pemilik.tenants.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.tenants.*') ? 'bg-indigo-600' : '' }}">Manajemen Penghuni</a>
                    <a href="{{ route('pemilik.invoices.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.invoices.*') ? 'bg-indigo-600' : '' }}">Manajemen Tagihan</a>
                    <a href="{{ route('pengurus.cleaning.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.cleaning.index') ? 'bg-indigo-600' : '' }}">Jadwal Kebersihan</a>
                    <a href="{{ route('pengurus.cleaning.report') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.cleaning.report') ? 'bg-indigo-600' : '' }}">Laporan Kebersihan</a>
                    <a href="{{ route('pengurus.damage_reports.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.damage_reports.*') ? 'bg-indigo-600' : '' }}">Laporan Kerusakan</a>
                    <a href="{{ route('password_resets.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('password_resets.*') ? 'bg-indigo-600' : '' }}">
                        Bantuan Reset Password
                    </a>
                    <a href="{{ route('pengurus.damage_reports.history') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.damage_reports.history') ? 'bg-indigo-600' : '' }}">Riwayat Kerusakan</a>
                @endif

                @if($role === 'penghuni')
                    <a href="{{ route('penghuni.invoices.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('penghuni.invoices.*') ? 'bg-indigo-600' : '' }}">Tagihan Saya</a>
                    <a href="{{ route('penghuni.cleaning.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('penghuni.cleaning.index') ? 'bg-indigo-600' : '' }}">Jadwal Kebersihan</a>
                    <a href="{{ route('penghuni.cleaning.report') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('penghuni.cleaning.report') ? 'bg-indigo-600' : '' }}">Laporan Kebersihan</a>
                    <a href="{{ route('penghuni.damage_reports.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('penghuni.damage_reports.*') ? 'bg-indigo-600' : '' }}">Laporan Kerusakan</a>
                @endif
            </nav>
            <div class="p-4 border-t border-indigo-600">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left p-3 rounded hover:bg-indigo-600">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">@yield('header')</h2>
                <div class="text-gray-600">
                    Selamat datang, <span class="font-bold">{{ auth()->user()->name }}</span>
                </div>
            </header>

            <div class="p-6">
                @if(session('success'))
                    <div id="flash-alert-success" class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-5 shadow-xs transition-all duration-300 transform translate-y-0 opacity-100">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="dismissAlert('flash-alert-success')" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg hover:bg-emerald-100/50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div id="flash-alert-error" class="flex items-center justify-between bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5 shadow-xs transition-all duration-300 transform translate-y-0 opacity-100">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="dismissAlert('flash-alert-error')" class="text-red-500 hover:text-red-700 p-1 rounded-lg hover:bg-red-100/50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function dismissAlert(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => el.remove(), 300);
        }
    </script>
</body>
</html>
