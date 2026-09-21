<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Petugas') — Sipkesma</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-800 flex antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden">
    </div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static shrink-0">
        
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-800">
            <div class="w-9 h-9 rounded-full bg-teal-500 text-white flex items-center justify-center shadow-md shadow-teal-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v3m-1.5-1.5h3" />
                </svg>
            </div>
            <div>
                <div class="text-base font-bold text-white tracking-tight leading-tight">Sipkesma</div>
                <div class="text-[10px] font-medium text-teal-400 uppercase tracking-wider">Panel Petugas & Admin</div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
            <div>
                <div class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Utama</div>
                <nav class="space-y-1">
                    <a href="{{ route('staff.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.dashboard') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        <span>Dashboard Ringkasan</span>
                    </a>

                    <a href="{{ route('staff.payments.index') }}" 
                       class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.payments.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                            <span>Verifikasi Bayar</span>
                        </div>
                        @php $pendingCount = \App\Models\Payment::where('status', 'pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-900">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('staff.checkin.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.checkin.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" /></svg>
                        <span>Meja Check-in (QR)</span>
                    </a>

                    <a href="{{ route('staff.queue.display') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.queue.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" /></svg>
                        <span>Layar Panggilan Antrean</span>
                    </a>
                </nav>
            </div>

            <!-- Stasiun Pemeriksaan Section -->
            <div>
                <div class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">3 Stasiun Medis</div>
                <nav class="space-y-1">
                    <a href="{{ route('staff.station.index', 'urin') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ (request()->routeIs('staff.station.*') && request()->route('station_name') === 'urin') ? 'bg-teal-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>Stasiun 1: Tes Urin</span>
                    </a>

                    <a href="{{ route('staff.station.index', 'napza') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ (request()->routeIs('staff.station.*') && request()->route('station_name') === 'napza') ? 'bg-teal-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        <span>Stasiun 2: Tes NAPZA</span>
                    </a>

                    <a href="{{ route('staff.station.index', 'pemeriksaan') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ (request()->routeIs('staff.station.*') && request()->route('station_name') === 'pemeriksaan') ? 'bg-teal-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Stasiun 3: Fisik & Dokter</span>
                    </a>
                </nav>
            </div>

            <!-- Manajemen Data -->
            <div>
                <div class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Manajemen</div>
                <nav class="space-y-1">
                    <a href="{{ route('staff.participants.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.participants.*') ? 'bg-teal-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        <span>Rekap Peserta PPKMB</span>
                    </a>

                    <a href="{{ route('staff.sessions.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('staff.sessions.*') ? 'bg-teal-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        <span>Kelola Kuota Jadwal</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Staff Footer -->
        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('student.beranda') }}" class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-teal-300 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                <span>Lihat Portal Mahasiswa</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <h1 class="text-lg font-bold text-slate-800">@yield('header_title', 'Panel Petugas')</h1>
            </div>

            <!-- Profile & Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden md:flex flex-col text-right">
                    <span class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] font-semibold text-teal-600 uppercase tracking-wide">
                        {{ Auth::user()->roles->first()?->name === 'admin' ? 'Administrator' : 'Petugas Medis' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs mb-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs mb-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3 shadow-xs mb-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    <div class="text-sm font-medium">{{ session('warning') }}</div>
                </div>
            @endif
        </div>

        <!-- Content Area -->
        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
