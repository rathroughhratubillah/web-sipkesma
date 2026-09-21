<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipkesma — Sistem Pelayanan Kesehatan PPKMB UINSSC</title>
    
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
            background-color: #f6fbfa;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased selection:bg-teal-500 selection:text-white">

    <!-- Demo Role Fast Switcher Banner -->
    <div class="bg-slate-900 text-slate-300 text-xs py-1.5 px-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    Akses Cepat Demo
                </span>
                <span class="text-slate-400">Masuk langsung ke akun contoh:</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('demo.login', 'ratu') }}" class="px-2.5 py-1 rounded bg-teal-800/80 hover:bg-teal-700 text-teal-200 transition">
                    Mahasiswa Baru (Ratu)
                </a>
                <a href="{{ route('demo.login', 'budi') }}" class="px-2.5 py-1 rounded bg-teal-800/80 hover:bg-teal-700 text-teal-200 transition">
                    Mahasiswa Bertiket (Budi)
                </a>
                <a href="{{ route('demo.login', 'siti') }}" class="px-2.5 py-1 rounded bg-teal-800/80 hover:bg-teal-700 text-teal-200 transition">
                    Mahasiswa Bersertifikat (Siti)
                </a>
                <a href="{{ route('demo.login', 'staff') }}" class="px-2.5 py-1 rounded bg-amber-800/80 hover:bg-amber-700 text-amber-200 transition">
                    Petugas Medis
                </a>
                <a href="{{ route('demo.login', 'admin') }}" class="px-2.5 py-1 rounded bg-purple-800/80 hover:bg-purple-700 text-purple-200 transition">
                    Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Header Navigation matching Screenshot 1 & 2 -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-7 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-500/20 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v3m-1.5-1.5h3" />
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold tracking-tight text-slate-900 leading-tight">Sipkesma</div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Skrining Kesehatan PPKMB</div>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#alur" class="hover:text-teal-600 transition">Alur Layanan</a>
                <a href="#mahasiswa" class="hover:text-teal-600 transition">Untuk Mahasiswa</a>
                <a href="#petugas" class="hover:text-teal-600 transition">Untuk Petugas</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    @if(Auth::user()->hasAnyRole(['staff', 'admin']))
                        <a href="{{ route('staff.dashboard') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 shadow-sm transition">
                            Panel Petugas
                        </a>
                    @else
                        <a href="{{ route('student.beranda') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 shadow-sm transition">
                            Beranda Saya
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-teal-600 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold shadow-md shadow-teal-500/20 transition">
                        Daftar Skrining
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section matching Screenshot 1 & 2 -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Left Copy -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Badge Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-teal-50 border border-teal-200 text-teal-700 text-xs font-semibold">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    <span>Layanan Kesehatan PPKMB</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                    Sistem Pelayanan Kesehatan PPKMB UINSSC
                </h1>

                <!-- Description -->
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl">
                    Sipkesma menyatukan pendaftaran, riwayat kesehatan, jadwal tes, pembayaran, antrian, hasil tes urin & NAPZA dan pemeriksaan fisik dalam satu alur yang jelas.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-base font-semibold shadow-lg shadow-teal-600/25 transition">
                        Mulai Pendaftaran
                    </a>
                    <a href="{{ route('demo.login', 'admin') }}" class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-base font-semibold shadow-xs transition">
                        Masuk sebagai Admin
                    </a>
                </div>

                <!-- Quick highlights -->
                <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 text-left">
                    <div>
                        <div class="text-2xl font-extrabold text-teal-600">100%</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Digital & Terpusat</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-teal-600">3 Stasiun</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Urin, NAPZA & Fisik</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-teal-600">QR Valid</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Sertifikat Resmi PDF</div>
                    </div>
                </div>
            </div>

            <!-- Hero Right Illustration matching Screenshot 1 & 2 -->
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-teal-50">
                    <img src="{{ asset('images/hero-illustration.jpg') }}" 
                         alt="Ilustrasi Antrean Skrining Kesehatan PPKMB Sipkesma" 
                         class="w-full h-auto object-cover rounded-2xl transform hover:scale-[1.02] transition duration-500">
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Alur Layanan Sipkesma matching Screenshot 3 -->
    <section id="alur" class="py-16 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="max-w-3xl mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Alur layanan Sipkesma
                </h2>
                <p class="text-slate-600 mt-2 text-base">
                    Setiap tahap terkunci rapi: mahasiswa tahu posisinya, petugas tahu siapa yang siap diperiksa.
                </p>
            </div>

            <!-- 8 Alur Cards Grid matching Screenshot 3 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- 01: Registrasi & Login -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">01</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Registrasi & Login</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Buat akun dengan email kampus.</p>
                </div>

                <!-- 02: Data Diri & Riwayat -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">02</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Data Diri & Riwayat</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Lengkapi profil dan riwayat kesehatan.</p>
                </div>

                <!-- 03: Pilih Jadwal Tes -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">03</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pilih Jadwal Tes</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Pilih tanggal dan sesi sesuai kuota.</p>
                </div>

                <!-- 04: Bayar & Verifikasi -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">04</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Bayar & Verifikasi</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Unggah bukti bayar, petugas memverifikasi.</p>
                </div>

                <!-- 05: Nomor Urut Tes -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">05</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Nomor Urut Tes</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Kartu antrian berisi QR untuk check-in.</p>
                </div>

                <!-- 06: Check-in Kampus -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 003.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0120.25 6v1.5m0 9V18A2.25 2.25 0 0118 20.25h-1.5m-9 0H6A2.25 2.25 0 013.75 18v-1.5" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">06</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Check-in Kampus</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Petugas memindai QR di lokasi tes.</p>
                </div>

                <!-- 07: Tes & Pemeriksaan -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.942a4.5 4.5 0 01-4.46 0L12 15.2m7.8.1c.42-.252.6-.775.424-1.226l-1.127-2.88a2.25 2.25 0 00-1.198-1.239M5 14.5l1.57.942a4.5 4.5 0 004.46 0L12 14.4m-7 .1c-.42-.252-.6-.775-.424-1.226l1.127-2.88A2.25 2.25 0 016.901 9.155" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">07</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Tes & Pemeriksaan</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Tes urin, tes NAPZA, pemeriksaan fisik.</p>
                </div>

                <!-- 08: Hasil & Sertifikat -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">08</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Hasil & Sertifikat</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Pantau status, unduh sertifikat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Untuk Mahasiswa & Untuk Petugas matching Screenshot 4 -->
    <section class="py-16 bg-[#f6fbfa]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Card Untuk Mahasiswa -->
                <div id="mahasiswa" class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-2xl font-bold text-slate-900">Untuk Mahasiswa</h3>
                    <ul class="space-y-4 text-sm text-slate-600 font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Formulir data diri & riwayat kesehatan sekali isi</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Kartu antrian digital lengkap dengan QR check-in</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Pantau hasil tiap stasiun secara langsung</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Unduh sertifikat setelah pemeriksaan selesai</span>
                        </li>
                    </ul>

                    <div class="pt-2">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-800">
                            Daftar sebagai Mahasiswa Baru &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card Untuk Petugas Medis -->
                <div id="petugas" class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-2xl font-bold text-slate-900">Untuk Petugas Medis</h3>
                    <ul class="space-y-4 text-sm text-slate-600 font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Verifikasi pembayaran dan penomoran antrian otomatis</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Pemindai check-in dan pemanggilan peserta</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Input hasil tes urin, NAPZA, dan pemeriksaan fisik</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Rekap peserta dan pengelolaan jadwal sesi</span>
                        </li>
                    </ul>

                    <div class="pt-2">
                        <a href="{{ route('demo.login', 'staff') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-800">
                            Masuk ke Panel Petugas Medis &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer matching Screenshot 4 -->
    <footer class="bg-white border-t border-slate-100 py-8 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-slate-800 text-sm">Sipkesma</div>
                    <div class="text-[10px] uppercase font-semibold text-slate-400">SKRINING KESEHATAN PPKMB</div>
                </div>
            </div>
            <div class="text-slate-400 font-medium">
                Sipkesma — Sistem Pelayanan Kesehatan Mahasiswa &copy; {{ date('Y') }}
            </div>
        </div>
    </footer>

</body>
</html>
