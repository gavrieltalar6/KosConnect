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

                    <a href="{{ route('pemilik.pengurus.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.pengurus.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen Pengurus</span>
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

                    <a href="{{ route('pengurus.reminders.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.reminders.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Pengingat Iuran</span>
                    </a>

                    <a href="{{ route('pemilik.damage_reports.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.damage_reports.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.7 6.3a5 5 0 00-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 006.4-6.4L14 12l-2-2 2.7-3.7z"/>
                        </svg>
                        <span>Riwayat Kerusakan</span>
                    </a>

                    <a href="{{ route('pemilik.cleaning_reports.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.cleaning_reports.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5-6v16H4V4h16z"/>
                        </svg>
                        <span>Laporan Kebersihan</span>
                    </a>

                    <a href="{{ route('password_resets.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('password_resets.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm2-11V7a4 4 0 118 0v2"/>
                        </svg>
                        <span>Bantuan Reset Password</span>
                    </a>

                    <a href="{{ route('pemilik.profile.edit') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.profile.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Profil Pemilik</span>
                    </a>

                @endif

                @if($role === 'pengurus')

                    <a href="{{ route('pemilik.tenants.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.tenants.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 017.75-1.35M16 3.13a4 4 0 010 7.75M12 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen Penghuni</span>
                    </a>

                    <a href="{{ route('pemilik.invoices.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pemilik.invoices.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5h6m-6 4h6m-6 4h6m-9 7V4a1 1 0 011-1h12a1 1 0 011 1v16l-3-2-3 2-3-2-3 2-3-2z"/>
                        </svg>
                        <span>Manajemen Tagihan</span>
                    </a>

                    <a href="{{ route('pengurus.reminders.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.reminders.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Pengingat Iuran</span>
                    </a>

                    <a href="{{ route('pengurus.cleaning.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.cleaning.index') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2-2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
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

                    <a href="{{ route('password_resets.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('password_resets.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm2-11V7a4 4 0 118 0v2"/>
                        </svg>
                        <span>Bantuan Reset Password</span>
                    </a>

                    <a href="{{ route('pengurus.profile.edit') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('pengurus.profile.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Profil Pengurus</span>
                    </a>

                @endif

                @if($role === 'penghuni')

                    <a href="{{ route('penghuni.invoices.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.invoices.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5h6m-6 4h6m-6 4h6m-9 7V4a1 1 0 011-1h12a1 1 0 011 1v16l-3-2-3 2-3 2-3-2z"/>
                        </svg>
                        <span>Tagihan Saya</span>
                    </a>

                    <a href="{{ route('penghuni.reminders.index') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.reminders.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Pengingat Iuran</span>
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

                    <a href="{{ route('penghuni.profile.edit') }}"
                       class="{{ $linkClass }} {{ request()->routeIs('penghuni.profile.*') ? $activeClass : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Profil Penghuni</span>
                    </a>

                @endif

            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-indigo-600">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="{{ $linkClass }} w-full text-left">
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
