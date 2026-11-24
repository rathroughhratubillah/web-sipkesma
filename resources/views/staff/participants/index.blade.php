@extends('layouts.staff')

@section('title', 'Rekap Data Peserta PPKMB')
@section('header_title', 'Rekapitulasi Data Peserta PPKMB')

@section('content')
<div class="space-y-6">

    <!-- ===================================================================== -->
    <!-- HALAMAN 1: DAFTAR FAKULTAS (viewLevel === 'faculty')                  -->
    <!-- ===================================================================== -->
    @if($viewLevel === 'faculty')
        <!-- Header Halaman 1 -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <div>
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1.5">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-teal-600 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span>Admin</span>
                    </a>
                    <span>/</span>
                    <span class="text-teal-800 font-bold">Rekap Data Peserta (Pilih Fakultas)</span>
                </nav>

                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Rekapitulasi Data Peserta PPKMB</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Silakan pilih salah satu fakultas di bawah untuk melihat rincian jurusan dan data mahasiswa.
                </p>
            </div>

            <!-- Global Actions -->
            <div class="flex items-center gap-2 self-start md:self-center">
                <a href="{{ route('staff.participants.export', ['year' => $selectedYear]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Ekspor Semua (CSV Excel)</span>
                </a>
            </div>
        </div>

        <!-- Quick Search Bar & Year Filter -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('staff.participants.index') }}" class="flex flex-col sm:flex-row items-center gap-2">
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari langsung mahasiswa berdasarkan NISN, Nama, No. HP, No. Antrean di semua fakultas..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-slate-50/50 outline-hidden">
                </div>
                <div class="w-full sm:w-auto shrink-0">
                    <select name="year" class="w-full sm:w-auto px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/50 focus:border-teal-500 outline-none">
                        <option value="all">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (string)($selectedYear ?? '') === (string)$yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-xs">
                    Cari Global
                </button>
            </form>
        </div>

        <!-- Overview Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Mahasiswa Terdaftar</span>
                    <div class="text-3xl font-black text-slate-900 mt-0.5">{{ $totalAllParticipants }}</div>
                    <span class="text-xs text-teal-600 font-semibold">Seluruh Fakultas PPKMB</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-lg">
                    🎓
                </div>
            </div>

            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Fakultas</span>
                    <div class="text-3xl font-black text-slate-900 mt-0.5">{{ count($faculties) }}</div>
                    <span class="text-xs text-slate-500 font-semibold">Unit Akademik UIN SSC</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-lg">
                    🏛️
                </div>
            </div>

            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Program Studi</span>
                    @php
                        $totalMajorsCount = collect($facultyMajorMap)->flatten()->count();
                    @endphp
                    <div class="text-3xl font-black text-slate-900 mt-0.5">{{ $totalMajorsCount }}</div>
                    <span class="text-xs text-purple-600 font-semibold">Pilihan Jurusan Terdata</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-black text-lg">
                    📚
                </div>
            </div>
        </div>

        <!-- Daftar 5 Fakultas Cards -->
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-black flex items-center justify-center shadow-xs">1</span>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Daftar Fakultas (Pilih Salah Satu)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $facultyMeta = [
                        'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)' => [
                            'abbr' => 'FITK', 'bg' => 'from-teal-500/10 to-teal-500/5', 'border' => 'border-teal-200 hover:border-teal-500', 'badge' => 'bg-teal-600 text-white', 'icon' => '👨‍🏫',
                        ],
                        'Fakultas Ekonomi Dan Bisnis Islam (FEBI)' => [
                            'abbr' => 'FEBI', 'bg' => 'from-blue-500/10 to-blue-500/5', 'border' => 'border-blue-200 hover:border-blue-500', 'badge' => 'bg-blue-600 text-white', 'icon' => '📈',
                        ],
                        'Fakultas Syariah' => [
                            'abbr' => 'SYARIAH', 'bg' => 'from-emerald-500/10 to-emerald-500/5', 'border' => 'border-emerald-200 hover:border-emerald-500', 'badge' => 'bg-emerald-600 text-white', 'icon' => '⚖️',
                        ],
                        'Fakultas Dakwah Dan Komunikasi Islam (FDKI)' => [
                            'abbr' => 'FDKI', 'bg' => 'from-amber-500/10 to-amber-500/5', 'border' => 'border-amber-200 hover:border-amber-500', 'badge' => 'bg-amber-600 text-white', 'icon' => '📢',
                        ],
                        'Fakultas Ushuluddin Dan Arab' => [
                            'abbr' => 'FUDA', 'bg' => 'from-purple-500/10 to-purple-500/5', 'border' => 'border-purple-200 hover:border-purple-500', 'badge' => 'bg-purple-600 text-white', 'icon' => '📖',
                        ],
                    ];
                @endphp

                @foreach($faculties as $fac)
                    @php
                        $count = $facultyStats[$fac] ?? 0;
                        $meta = $facultyMeta[$fac] ?? [
                            'abbr' => 'FAK', 'bg' => 'from-slate-500/10 to-slate-500/5', 'border' => 'border-slate-200 hover:border-slate-400', 'badge' => 'bg-slate-600 text-white', 'icon' => '🏛️',
                        ];
                        $majorsCount = count($facultyMajorMap[$fac] ?? []);
                    @endphp
                    <a href="{{ route('staff.participants.index', ['faculty' => $fac, 'year' => $selectedYear]) }}"
                       class="group bg-white p-6 rounded-3xl border {{ $meta['border'] }} shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-xl {{ $meta['badge'] }}">
                                    {{ $meta['abbr'] }}
                                </span>
                                <span class="text-2xl">{{ $meta['icon'] }}</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 group-hover:text-teal-700 transition leading-snug">
                                {{ $fac }}
                            </h3>

                            <div class="mt-4 flex items-center gap-3 text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">{{ $majorsCount }} Program Studi</span>
                                <span>•</span>
                                <span class="font-bold px-2 py-0.5 rounded-full {{ $count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $count }} Mahasiswa
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-700 group-hover:translate-x-1 transition-transform flex items-center gap-1.5">
                                <span>Lihat Jurusan</span>
                                <span>&rarr;</span>
                            </span>
                            <span class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-teal-600 group-hover:text-white text-slate-400 flex items-center justify-center transition text-sm">
                                &rarr;
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 2: DAFTAR JURUSAN (viewLevel === 'major')                     -->
    <!-- ===================================================================== -->
    @elseif($viewLevel === 'major')
        <!-- Header & Navigasi Kembali Halaman 2 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <a href="{{ route('staff.participants.index', ['year' => $selectedYear]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    <span>&larr; Kembali ke Daftar Fakultas</span>
                </a>

                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 flex-wrap">
                    <a href="{{ route('staff.participants.index', ['year' => $selectedYear]) }}" class="hover:text-teal-600 transition">Rekap Peserta (Fakultas)</a>
                    <span>/</span>
                    <span class="text-teal-800 font-bold">{{ $selectedFaculty }}</span>
                </nav>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2 border-t border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-black flex items-center justify-center shadow-xs">2</span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Pilih Program Studi / Jurusan
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Fakultas: <strong class="text-teal-800 font-bold">{{ $selectedFaculty }}</strong> • Total: <strong class="text-slate-800">{{ $facultyStats[$selectedFaculty] ?? 0 }}</strong> Mahasiswa Terdaftar
                    </p>
                </div>

                <a href="{{ route('staff.participants.export', ['faculty' => $selectedFaculty, 'year' => $selectedYear]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition self-start md:self-auto">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Ekspor Fakultas Ini (CSV)</span>
                </a>
            </div>
        </div>

        <!-- Grid of Jurusan Cards -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Pencet salah satu jurusan di bawah untuk membuka tabel mahasiswa:
                </span>
                <span class="text-xs text-slate-400">{{ count($currentMajors) }} Jurusan Terdaftar</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @forelse($currentMajors as $maj)
                    @php
                        $mCount = $majorStats[$maj] ?? 0;
                    @endphp
                    <a href="{{ route('staff.participants.index', ['faculty' => $selectedFaculty, 'major' => $maj, 'year' => $selectedYear]) }}"
                       class="group bg-white p-5 rounded-3xl border transition-all duration-200 flex flex-col justify-between
                              {{ $mCount > 0 
                                 ? 'border-teal-200 hover:border-teal-500 hover:shadow-md hover:-translate-y-0.5' 
                                 : 'border-slate-200 hover:border-slate-300 hover:shadow-xs' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $mCount > 0 ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $mCount > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $mCount }} Mahasiswa
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-700 transition leading-snug">
                                {{ $maj }}
                            </h3>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="font-bold {{ $mCount > 0 ? 'text-teal-700' : 'text-slate-400' }} group-hover:translate-x-1 transition-transform">
                                Buka Tabel Mahasiswa &rarr;
                            </span>
                            <span class="w-7 h-7 rounded-full bg-slate-50 group-hover:bg-teal-600 group-hover:text-white text-slate-400 flex items-center justify-center transition text-xs">
                                &rarr;
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full p-8 text-center bg-white rounded-3xl border border-slate-200 text-slate-400 text-xs">
                        Tidak ada jurusan yang terdaftar pada fakultas ini.
                    </div>
                @endforelse
            </div>
        </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 3: TABEL MAHASISWA JURUSAN (viewLevel === 'student')          -->
    <!-- ===================================================================== -->
    @elseif($viewLevel === 'student')
        <!-- Header & Navigasi Kembali Halaman 3 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <a href="{{ route('staff.participants.index', ['faculty' => $selectedFaculty]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    <span>&larr; Kembali ke Daftar Jurusan ({{ $selectedFaculty }})</span>
                </a>

                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 flex-wrap">
                    <a href="{{ route('staff.participants.index') }}" class="hover:text-teal-600 transition">Rekap Peserta</a>
                    <span>/</span>
                    <a href="{{ route('staff.participants.index', ['faculty' => $selectedFaculty]) }}" class="hover:text-teal-600 transition">{{ $selectedFaculty }}</a>
                    <span>/</span>
                    <span class="text-teal-800 font-bold">{{ $selectedMajor }}</span>
                </nav>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2 border-t border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-black flex items-center justify-center shadow-xs">3</span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Tabel Mahasiswa — Program Studi <span class="text-teal-700">{{ $selectedMajor }}</span>
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                            {{ $participants ? $participants->total() : 0 }} Mahasiswa Terdaftar
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Fakultas: <strong class="text-slate-800">{{ $selectedFaculty }}</strong>
                    </p>
                </div>

                <!-- Export Specific Major Button -->
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <a href="{{ route('staff.participants.export', ['faculty' => $selectedFaculty, 'major' => $selectedMajor]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Ekspor CSV (Jurusan Ini)</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card khusus Jurusan Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <form method="GET" action="{{ route('staff.participants.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <input type="hidden" name="faculty" value="{{ $selectedFaculty }}">
                <input type="hidden" name="major" value="{{ $selectedMajor }}">

                <!-- Search Input -->
                <div class="sm:col-span-6 lg:col-span-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </span>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari NISN, Nama, No. HP, No. Antrean di jurusan ini..."
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-slate-50/50 outline-hidden">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-4 lg:col-span-4">
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-semibold bg-white outline-hidden">
                        <option value="all">-- Semua Status Pendaftaran --</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Form Belum Lengkap)</option>
                        <option value="awaiting_payment" {{ $status === 'awaiting_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
                        <option value="awaiting_verification" {{ $status === 'awaiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi Pembayaran</option>
                        <option value="cleared" {{ $status === 'cleared' ? 'selected' : '' }}>Cleared (Tiket Antrean Terbit)</option>
                        <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Checked In (Hadir di Klinik)</option>
                        <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Sedang Diperiksa</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed (Lulus Skrining)</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition">
                        Filter
                    </button>
                    @if($search || ($status && $status !== 'all'))
                        <a href="{{ route('staff.participants.index', ['faculty' => $selectedFaculty, 'major' => $selectedMajor]) }}"
                           class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Mahasiswa -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">Antrean</th>
                            <th class="p-3.5">NISN &amp; Mahasiswa</th>
                            <th class="p-3.5">Kontak &amp; Gender</th>
                            <th class="p-3.5">Sesi Skrining</th>
                            <th class="p-3.5">Status Registrasi</th>
                            <th class="p-3.5 text-center">Tes Buta Warna</th>
                            <th class="p-3.5 text-center">Tes NAPZA</th>
                            <th class="p-3.5 text-center">Fisik/Dokter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($participants as $reg)
                            @php
                                $u = $reg->user;
                                $prof = $u?->profile;
                                $urin = $reg->getStationResult('urin');
                                $napza = $reg->getStationResult('napza');
                                $fisik = $reg->getStationResult('pemeriksaan');
                            @endphp
                            <tr class="hover:bg-teal-50/40 transition">
                                <td class="p-3.5">
                                    <span class="font-bold font-mono text-teal-900 bg-teal-100/70 border border-teal-200 px-2.5 py-0.5 rounded-md text-[11px]">
                                        {{ $reg->queue_code ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">{{ $prof?->full_name ?: $u?->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $prof?->nim ?: '—' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $u?->email }}</div>
                                </td>
                                <td class="p-3.5">
                                    <div class="text-slate-700 font-semibold">{{ $prof?->phone ?: '-' }}</div>
                                    <div class="text-[11px] text-slate-400">Gender: {{ $prof?->gender === 'L' ? 'Laki-laki' : ($prof?->gender === 'P' ? 'Perempuan' : '-') }}</div>
                                </td>
                                <td class="p-3.5 text-slate-600">
                                    {{ $reg->testSession?->session_name ?: 'Belum Pilih Jadwal' }}
                                </td>
                                <td class="p-3.5">
                                    @php
                                        $statusBadge = match($reg->status) {
                                            'completed'             => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cleared'               => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'checked_in'            => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            'in_progress'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'awaiting_verification' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'awaiting_payment'      => 'bg-orange-50 text-orange-700 border-orange-200',
                                            default                 => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                        {{ $reg->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urin?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($urin ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                        {{ $urin?->status ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $napza?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($napza ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                        {{ $napza?->status ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fisik?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($fisik ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                        {{ $fisik?->status ?: '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                        📋
                                    </div>
                                    <div class="font-bold text-slate-700">Belum ada mahasiswa yang terdaftar di Program Studi {{ $selectedMajor }}.</div>
                                    <div class="text-xs text-slate-400 mt-1">
                                        <a href="{{ route('staff.participants.index', ['faculty' => $selectedFaculty]) }}" class="text-teal-600 hover:underline">
                                            &larr; Pilih jurusan lain di Fakultas {{ $selectedFaculty }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($participants && $participants->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $participants->links() }}
                </div>
            @endif
        </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 4: HASIL PENCARIAN GLOBAL (viewLevel === 'search')             -->
    <!-- ===================================================================== -->
    @elseif($viewLevel === 'search')
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <a href="{{ route('staff.participants.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    &larr; Kembali ke Daftar Fakultas
                </a>
                <span class="text-xs text-slate-400">Pencarian di Seluruh Fakultas</span>
            </div>

            <div>
                <h1 class="text-lg font-black text-slate-900">Hasil Pencarian: "{{ $search }}"</h1>
                <p class="text-xs text-slate-500">Ditemukan {{ $participants ? $participants->total() : 0 }} mahasiswa yang cocok</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">Antrean</th>
                            <th class="p-3.5">NISN &amp; Mahasiswa</th>
                            <th class="p-3.5">Fakultas &amp; Program Studi</th>
                            <th class="p-3.5">Status Registrasi</th>
                            <th class="p-3.5 text-center">Tes Buta Warna</th>
                            <th class="p-3.5 text-center">NAPZA</th>
                            <th class="p-3.5 text-center">Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($participants as $reg)
                            @php
                                $u = $reg->user;
                                $prof = $u?->profile;
                                $urin = $reg->getStationResult('urin');
                                $napza = $reg->getStationResult('napza');
                                $fisik = $reg->getStationResult('pemeriksaan');
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5">
                                    <span class="font-bold font-mono text-teal-900 bg-teal-100/70 border border-teal-200 px-2 py-0.5 rounded-md text-[11px]">
                                        {{ $reg->queue_code ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">{{ $prof?->full_name ?: $u?->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $prof?->nim ?: '—' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-semibold text-slate-800">{{ $prof?->faculty ?: '-' }}</div>
                                    <a href="{{ route('staff.participants.index', ['faculty' => $prof?->faculty, 'major' => $prof?->major]) }}"
                                       class="text-[11px] font-bold text-teal-700 hover:underline inline-block mt-0.5">
                                        {{ $prof?->major ?: '-' }} &rarr;
                                    </a>
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $reg->status }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urin?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $urin?->status ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $napza?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $napza?->status ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fisik?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $fisik?->status ?: '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada mahasiswa yang cocok dengan pencarian "{{ $search }}".</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($participants && $participants->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $participants->links() }}</div>
            @endif
        </div>
    @endif

</div>
@endsection
