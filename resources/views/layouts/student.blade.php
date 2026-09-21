<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda Peserta') — Sipkesma</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js fallback CDN if vite bundling takes a moment -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f6fbfa;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-800 flex flex-col antialiased">
    
    <!-- Demo Quick Switcher Banner (For easy grading & multi-role testing) -->
    <div class="bg-slate-900 text-slate-200 text-xs py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    Mode Pengujian / Demo
                </span>
                <span class="hidden sm:inline text-slate-400">Pilih profil uji coba langsung:</span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('demo.login', 'ratu') }}" class="px-2 py-1 rounded bg-teal-800/60 hover:bg-teal-700 text-teal-200 transition">
                    Ratu (Baru / Draft)
                </a>
                <a href="{{ route('demo.login', 'budi') }}" class="px-2 py-1 rounded bg-teal-800/60 hover:bg-teal-700 text-teal-200 transition">
                    Budi (Tiket Antrean)
                </a>
                <a href="{{ route('demo.login', 'siti') }}" class="px-2 py-1 rounded bg-teal-800/60 hover:bg-teal-700 text-teal-200 transition">
                    Siti (Lulus Sertifikat)
                </a>
                <a href="{{ route('demo.login', 'staff') }}" class="px-2 py-1 rounded bg-amber-800/60 hover:bg-amber-700 text-amber-200 transition">
                    dr. Nurul (Petugas Medis)
                </a>
                <a href="{{ route('demo.login', 'admin') }}" class="px-2 py-1 rounded bg-purple-800/60 hover:bg-purple-700 text-purple-200 transition">
                    Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white border-b border-slate-100 shadow-xs sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top brand & logout row -->
            <div class="flex items-center justify-between py-4 border-b border-slate-50">
                <!-- Brand Logo matching screenshots -->
                <a href="{{ route('student.beranda') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-500/20 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v3m-1.5-1.5h3" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold tracking-tight text-slate-900 leading-tight">Sipkesma</div>
                        <div class="text-[10px] font-semibold tracking-wider text-slate-400 uppercase">Skrining Kesehatan PPKMB</div>
                    </div>
                </a>

                <!-- User identity & Logout -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-slate-800">{{ Auth::user()->name }}</span>
                        <span class="text-[11px] text-slate-500">{{ Auth::user()->profile?->nim ?? Auth::user()->email }}</span>
                    </div>

                    @if(Auth::user()->hasAnyRole(['staff', 'admin']))
                        <a href="{{ route('staff.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 text-xs font-semibold border border-teal-200 hover:bg-teal-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                            Panel Petugas
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab Pills Navigation matching Screenshot 5 -->
            <nav class="flex items-center gap-1.5 py-2.5 overflow-x-auto scrollbar-none text-xs font-semibold">
                @php
                    $navItems = [
                        ['route' => 'student.beranda', 'label' => 'Beranda', 'prefix' => 'beranda'],
                        ['route' => 'student.profile', 'label' => 'Data Diri', 'prefix' => 'profil'],
                        ['route' => 'student.health_history', 'label' => 'Riwayat', 'prefix' => 'riwayat-kesehatan'],
                        ['route' => 'student.schedule', 'label' => 'Jadwal', 'prefix' => 'jadwal'],
                        ['route' => 'student.confirmation', 'label' => 'Konfirmasi', 'prefix' => 'konfirmasi'],
                        ['route' => 'student.payment', 'label' => 'Pembayaran', 'prefix' => 'pembayaran'],
                        ['route' => 'student.ticket', 'label' => 'Tiket', 'prefix' => 'tiket'],
                        ['route' => 'student.status', 'label' => 'Status', 'prefix' => 'status'],
                        ['route' => 'student.certificate', 'label' => 'Sertifikat', 'prefix' => 'sertifikat'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="px-3.5 py-1.5 rounded-full whitespace-nowrap transition {{ $isActive ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:text-teal-700 hover:bg-teal-50/70' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs mb-4">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs mb-4">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3 shadow-xs mb-4">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <div class="text-sm font-medium">{{ session('warning') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 flex items-start gap-3 shadow-xs mb-4">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer matching Screenshot 4 -->
    <footer class="bg-white border-t border-slate-100 py-6 mt-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-teal-600 text-white flex items-center justify-center text-[10px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-slate-800">Sipkesma</span>
                    <span class="text-slate-400 ml-1.5">— Skrining Kesehatan PPKMB</span>
                </div>
            </div>
            <div class="text-slate-400">
                Sipkesma — Sistem Pelayanan Kesehatan Mahasiswa &copy; {{ date('Y') }} UINSSC
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
