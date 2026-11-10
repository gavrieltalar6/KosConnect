<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                @endif

                @if($role === 'pengurus')
                    <a href="{{ route('pemilik.invoices.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pemilik.invoices.*') ? 'bg-indigo-600' : '' }}">Manajemen Tagihan</a>
                    <a href="{{ route('pengurus.cleaning.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.cleaning.index') ? 'bg-indigo-600' : '' }}">Jadwal Kebersihan</a>
                    <a href="{{ route('pengurus.cleaning.report') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.cleaning.report') ? 'bg-indigo-600' : '' }}">Laporan Kebersihan</a>
                    <a href="{{ route('pengurus.damage_reports.index') }}" class="block p-3 rounded hover:bg-indigo-600 {{ request()->routeIs('pengurus.damage_reports.*') ? 'bg-indigo-600' : '' }}">Laporan Kerusakan</a>
                @endif

                @if($role === 'penghuni')
                    <a href="#" class="block p-3 rounded hover:bg-indigo-600">Tagihan Saya</a>
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
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
