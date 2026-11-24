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

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f6fbfa;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased selection:bg-teal-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40">
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
                <a href="#admin" class="hover:text-teal-600 transition">Untuk Administrator</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 shadow-sm transition">
                            Panel Admin
                        </a>
                    @else
                        <a href="{{ route('student.beranda') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 shadow-sm transition">
                            Portal Mahasiswa
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
                    Sipkesma mengintegrasikan pendaftaran data diri, pengisian riwayat kesehatan, reservasi jadwal, verifikasi pembayaran, tiket antrean digital, hingga pelaksanaan 3 stasiun pemeriksaan (tes buta warna, skrining NAPZA, dan pemeriksaan fisik) dalam satu alur terpadu.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-base font-semibold shadow-lg shadow-teal-600/25 transition">
                        Mulai Pendaftaran
                    </a>
                    <a href="#alur" class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-base font-semibold shadow-xs transition">
                        Pelajari Alur Skrining
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
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Buta Warna, NAPZA & Fisik</div>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-teal-600">QR Valid</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Tiket Antrean PDF</div>
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
                    Alur Layanan Skrining Kesehatan
                </h2>
                <p class="text-slate-600 mt-2 text-base">
                    Alur terpadu dan terstruktur: mulai dari pengisian data mandiri secara online hingga pelaksanaan 3 stasiun pemeriksaan langsung di klinik kampus.
                </p>
            </div>

            <!-- 8 Alur Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- 01: Registrasi Akun -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">01</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Registrasi Akun</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Buat akun pendaftaran dengan login 1-klik Google atau email aktif mahasiswa baru.</p>
                </div>

                <!-- 02: Lengkapi Data Diri -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.364a4.125 4.125 0 00-6.338 0 .375.375 0 00.285.611h5.768a.375.375 0 00.285-.611z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">02</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Lengkapi Data Diri</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Isi identitas diri, NISN/NIM, fakultas, program studi, dan nomor WhatsApp aktif.</p>
                </div>

                <!-- 03: Riwayat Kesehatan -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">03</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Riwayat Kesehatan</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Isi kuesioner medis awal, riwayat alergi, dan penyakit sebagai rujukan dokter klinik.</p>
                </div>

                <!-- 04: Pilih Jadwal Sesi -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">04</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pilih Jadwal Sesi</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Reservasi tanggal dan jam kedatangan di klinik kampus sesuai sisa kuota harian.</p>
                </div>

                <!-- 05: Pembayaran Skrining -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">05</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pembayaran Skrining</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Transfer biaya melalui SeaBank atau DANA QRIS, lalu unggah foto bukti transfer.</p>
                </div>

                <!-- 06: Verifikasi & Tiket QR -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">06</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Verifikasi & Tiket QR</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Admin memvalidasi pembayaran, nomor antrean resmi dan tiket digital ber-QR langsung terbit.</p>
                </div>

                <!-- 07: Pemeriksaan 3 Stasiun -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.942a4.5 4.5 0 01-4.46 0L12 15.2m7.8.1c.42-.252.6-.775.424-1.226l-1.127-2.88a2.25 2.25 0 00-1.198-1.239M5 14.5l1.57.942a4.5 4.5 0 004.46 0L12 14.4m-7 .1c-.42-.252-.6-.775-.424-1.226l1.127-2.88A2.25 2.25 0 016.901 9.155" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">07</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pemeriksaan 3 Stasiun</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Hadir di klinik kampus menjalani Tes Buta Warna, Skrining NAPZA, serta Fisik & Dokter.</p>
                </div>

                <!-- 08: Selesai Skrining -->
                <div class="p-6 rounded-2xl bg-white border border-teal-100/80 shadow-xs hover:shadow-md hover:border-teal-300 transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">08</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Selesai Skrining</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Seluruh tahapan skrining selesai dan data rekapitulasi tersimpan resmi di sistem kampus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Untuk Mahasiswa & Untuk Administrator -->
    <section class="py-16 bg-[#f6fbfa]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Card Untuk Mahasiswa -->
                <div id="mahasiswa" class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xl shadow-2xs">
                            🎓
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">Untuk Mahasiswa Baru</h3>
                            <p class="text-xs text-slate-500">Akses mandiri praktis selama masa orientasi PPKMB</p>
                        </div>
                    </div>

                    <ul class="space-y-4 text-sm text-slate-600 font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Pendaftaran mudah via login 1-klik akun Google atau email aktif</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Formulir data diri & riwayat kesehatan terpadu tanpa berkas kertas</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Pilihan jadwal sesi kedatangan klinik fleksibel sesuai sisa kuota</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Tiket antrean digital resmi ber-QR setelah pembayaran diverifikasi admin</span>
                        </li>
                    </ul>

                    <div class="pt-2">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-800">
                            Daftar sebagai Mahasiswa Baru &rarr;
                        </a>
                    </div>
                </div>

                <!-- Card Untuk Administrator -->
                <div id="admin" class="p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xl shadow-2xs">
                            🩺
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">Untuk Administrator & Pengelola</h3>
                            <p class="text-xs text-slate-500">Pusat kendali verifikasi dan pemantauan data skrining</p>
                        </div>
                    </div>

                    <ul class="space-y-4 text-sm text-slate-600 font-medium">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Verifikasi bukti pembayaran (SeaBank & DANA QRIS) dengan persetujuan manual</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Penerbitan otomatis kode antrean resmi setelah pembayaran disetujui</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Pengelolaan kuota kapasitas sesi jadwal skrining harian di klinik kampus</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <span>Monitoring dashboard pendaftaran, tren statistik, dan rekap peserta per fakultas</span>
                        </li>
                    </ul>

                    <div class="pt-2">
                        <a href="{{ route('login', ['role' => 'admin']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-purple-700 hover:text-purple-800">
                            Masuk ke Panel Administrator &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
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
            <div class="flex items-center gap-6">
                <a href="{{ route('login', ['role' => 'admin']) }}" class="text-slate-500 hover:text-teal-600 transition font-medium">
                    Portal Administrator
                </a>
                <span class="text-slate-300">|</span>
                <span class="text-slate-400 font-medium">
                    Sipkesma &copy; {{ date('Y') }} UINSSC
                </span>
            </div>
        </div>
    </footer>

</body>
</html>
