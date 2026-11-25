
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
        <aside class="w-64 bg-indigo-700 text-white flex flex-col shrink-0">

            <div class="p-6 text-2xl font-bold border-b border-indigo-600">
                {{ config('app.name') }}
            </div>

            <nav class="flex-grow p-4 space-y-2">

                @php
                    $role = auth()->user()->role?->value;
                    $linkClass = 'flex items-center gap-3 p-3 rounded-lg text-sm font-medium transition-colors hover:bg-indigo-600';
                    $activeClass = 'bg-indigo-600 shadow-sm';
                @endphp

                <!-- Dashboard -->
                <a href="{{ route($role . '.dashboard') }}"
                   class="{{ $linkClass }} {{ request()->routeIs($role . '.dashboard') ? $activeClass : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                @if($role === 'pemilik')

                    <a href="{{ route('pemilik.tenants.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.tenants.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 017.75-1.35M16 3.13a4 4 0 010 7.75M12 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen Penghuni</span>
                    </a>

                    <a href="{{ route('pemilik.rooms.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.rooms.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 21V7l9-4 9 4v14M3 10h18M7 21v-5h10v5"/>
                        </svg>
                        <span>Manajemen Kamar</span>
                    </a>

                    <a href="{{ route('pemilik.invoices.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.invoices.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5h6m-6 4h6m-6 4h6m-9 7V4a1 1 0 011-1h12a1 1 0 011 1v16l-3-2-3 2-3-2-3 2-3-2z"/>
                        </svg>
                        <span>Manajemen Tagihan</span>
                    </a>

                @endif

                @if($role === 'pengurus')

                    <a href="{{ route('pemilik.invoices.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.invoices.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5h6m-6 4h6m-6 4h6m-9 7V4a1 1 0 011-1h12a1 1 0 011 1v16l-3-2-3 2-3-2-3 2-3-2z"/>
                        </svg>
                        <span>Manajemen Tagihan</span>
                    </a>

                    <a href="{{ route('pengurus.cleaning.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.cleaning.index') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                        <span>Jadwal Kebersihan</span>
                    </a>

                    <a href="{{ route('pengurus.cleaning.report') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.cleaning.report') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5-6v16H4V4h16z"/>
                        </svg>
                        <span>Laporan Kebersihan</span>
                    </a>

                    <a href="{{ route('pengurus.damage_reports.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.damage_reports.index') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.7 6.3a5 5 0 00-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 006.4-6.4L14 12l-2-2 2.7-3.7z"/>
                        </svg>
                        <span>Laporan Kerusakan</span>
                    </a>

                    <a href="{{ route('pengurus.damage_reports.history') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.damage_reports.history') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.7 2.7L3 8m0-5v5h5m4-1v5l3 2"/>
                        </svg>
                        <span>Riwayat Kerusakan</span>
                    </a>

                @endif

                @if($role === 'penghuni')

                    <a href="#"
                       class="{{ $linkClass }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5h6m-6 4h6m-6 4h6m-9 7V4a1 1 0 011-1h12a1 1 0 011 1v16l-3-2-3 2-3-2-3 2-3-2z"/>
                        </svg>
                        <span>Tagihan Saya</span>
                    </a>

                    <a href="{{ route('penghuni.cleaning.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.cleaning.index') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2-2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                        <span>Jadwal Kebersihan</span>
                    </a>

                    <a href="{{ route('penghuni.cleaning.report') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.cleaning.report') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5-6v16H4V4h16z"/>
                        </svg>
                        <span>Laporan Kebersihan</span>
                    </a>

                    <a href="{{ route('penghuni.damage_reports.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.damage_reports.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.7 6.3a5 5 0 00-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 006.4-6.4L14 12l-2-2 2.7-3.7z"/>
                        </svg>
                        <span>Laporan Kerusakan</span>
                    </a>

                @endif

            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-indigo-600">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="{{ $linkClass }} w-full text-left">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col min-w-0">

            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">
                    @yield('header')
                </h2>

                <div class="text-gray-600">
                    Selamat datang,
                    <span class="font-bold">{{ auth()->user()->name }}</span>
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
