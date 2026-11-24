@extends('layouts.admin')

@section('title', 'Kelola Riwayat Kesehatan Mahasiswa')
@section('header_title', 'Kelola Riwayat Kesehatan')
@section('header_subtitle', 'Monitoring & rekapitulasi data kesehatan mahasiswa PPKMB')

@section('content')
<div class="space-y-6" x-data="{
    detailModalOpen: false,
    selectedStudent: null,
    openDetail(student) {
        this.selectedStudent = student;
        this.detailModalOpen = true;
    },
    closeDetail() {
        this.detailModalOpen = false;
        this.selectedStudent = null;
    }
}">

    <!-- ===================================================================== -->
    <!-- HALAMAN 1: DAFTAR FAKULTAS (viewMode === 'faculties')                 -->
    <!-- ===================================================================== -->
    @if($viewMode === 'faculties')
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
                    <span class="text-teal-800 font-bold">Riwayat Kesehatan (Pilih Fakultas)</span>
                </nav>

                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kelola Riwayat Kesehatan Mahasiswa</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Silakan pilih salah satu fakultas di bawah untuk menelusuri jurusan dan memeriksa data riwayat kesehatan mahasiswa.
                </p>
            </div>

            <!-- Global Actions -->
            <div class="flex items-center gap-2 self-start md:self-center">
                <a href="{{ route('admin.health.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Ekspor Semua Data (CSV)</span>
                </a>
            </div>
        </div>

        <!-- Quick Global Search -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.health.index') }}" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari langsung mahasiswa berdasarkan NIM, Nama, No. HP, atau Email di semua fakultas..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-slate-50/50 outline-hidden">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition shadow-xs">
                    Cari Global
                </button>
            </form>
        </div>

        <!-- Overview Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Mahasiswa Terdaftar</span>
                    <div class="text-3xl font-black text-slate-900 mt-0.5">{{ $totalStudents }}</div>
                    <span class="text-xs text-teal-600 font-semibold">Seluruh Fakultas UIN SSC</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-lg">
                    🎓
                </div>
            </div>

            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sudah Isi Riwayat</span>
                    <div class="text-3xl font-black text-emerald-700 mt-0.5">{{ $totalWithHealth }}</div>
                    <span class="text-xs text-emerald-600 font-bold">{{ $completionRate }}% Kelengkapan Berkas</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-lg">
                    🩺
                </div>
            </div>

            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Belum Mengisi Riwayat</span>
                    <div class="text-3xl font-black text-rose-600 mt-0.5">{{ $totalWithoutHealth }}</div>
                    <span class="text-xs text-slate-500 font-semibold">Perlu tindak lanjut skrining</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-black text-lg">
                    ⚠️
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
                            'abbr' => 'FITK', 'border' => 'border-teal-200 hover:border-teal-500', 'badge' => 'bg-teal-600 text-white', 'icon' => '👨‍🏫',
                        ],
                        'Fakultas Ekonomi Dan Bisnis Islam (FEBI)' => [
                            'abbr' => 'FEBI', 'border' => 'border-blue-200 hover:border-blue-500', 'badge' => 'bg-blue-600 text-white', 'icon' => '📈',
                        ],
                        'Fakultas Syariah' => [
                            'abbr' => 'SYARIAH', 'border' => 'border-emerald-200 hover:border-emerald-500', 'badge' => 'bg-emerald-600 text-white', 'icon' => '⚖️',
                        ],
                        'Fakultas Dakwah Dan Komunikasi Islam (FDKI)' => [
                            'abbr' => 'FDKI', 'border' => 'border-amber-200 hover:border-amber-500', 'badge' => 'bg-amber-600 text-white', 'icon' => '📢',
                        ],
                        'Fakultas Ushuluddin Dan Arab' => [
                            'abbr' => 'FUDA', 'border' => 'border-purple-200 hover:border-purple-500', 'badge' => 'bg-purple-600 text-white', 'icon' => '📖',
                        ],
                    ];
                @endphp

                @foreach(array_keys($faculties) as $fac)
                    @php
                        $stat = $facultyStats[$fac] ?? ['total' => 0, 'with_health' => 0, 'without_health' => 0, 'total_majors' => count($faculties[$fac] ?? [])];
                        $meta = $facultyMeta[$fac] ?? [
                            'abbr' => 'FAK', 'border' => 'border-slate-200 hover:border-slate-400', 'badge' => 'bg-slate-600 text-white', 'icon' => '🏛️',
                        ];
                        $percent = $stat['total'] > 0 ? round(($stat['with_health'] / $stat['total']) * 100) : 0;
                    @endphp
                    <a href="{{ route('admin.health.index', ['faculty' => $fac]) }}"
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

                            <div class="mt-4 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">{{ $stat['total_majors'] }} Program Studi</span>
                                    <span class="font-bold text-slate-800">{{ $stat['total'] }} Mahasiswa</span>
                                </div>

                                <!-- Progress Bar Pengisian -->
                                <div>
                                    <div class="flex items-center justify-between text-[11px] mb-1 font-semibold">
                                        <span class="text-emerald-700">{{ $stat['with_health'] }} Sudah Isi ({{ $percent }}%)</span>
                                        <span class="text-slate-400">{{ $stat['without_health'] }} Belum</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-700 group-hover:translate-x-1 transition-transform flex items-center gap-1.5">
                                <span>Pilih Jurusan</span>
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
    <!-- HALAMAN 2: DAFTAR JURUSAN (viewMode === 'departments')                 -->
    <!-- ===================================================================== -->
    @elseif($viewMode === 'departments')
        <!-- Header & Navigasi Kembali Halaman 2 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <a href="{{ route('admin.health.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    <span>&larr; Kembali ke Daftar Fakultas</span>
                </a>

                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 flex-wrap">
                    <a href="{{ route('admin.health.index') }}" class="hover:text-teal-600 transition">Riwayat Kesehatan</a>
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
                        Fakultas: <strong class="text-teal-800 font-bold">{{ $selectedFaculty }}</strong> • Total: <strong class="text-slate-800">{{ $currentFacultyData['total'] ?? 0 }}</strong> Mahasiswa Terdaftar (<strong class="text-emerald-700">{{ $currentFacultyData['with_health'] ?? 0 }}</strong> Sudah Isi Riwayat Kesehatan)
                    </p>
                </div>

                <a href="{{ route('admin.health.export', ['faculty' => $selectedFaculty]) }}"
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
                    Pencet salah satu jurusan di bawah untuk memunculkan tabel riwayat kesehatan:
                </span>
                <span class="text-xs text-slate-400">{{ count($faculties[$selectedFaculty] ?? []) }} Jurusan Terdaftar</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @php
                    $majorsList = $faculties[$selectedFaculty] ?? [];
                @endphp

                @forelse($majorsList as $maj)
                    @php
                        $mStat = $currentFacultyData['majors'][$maj] ?? ['total' => 0, 'with_health' => 0, 'without_health' => 0];
                        $hasStudents = $mStat['total'] > 0;
                    @endphp
                    <a href="{{ route('admin.health.index', ['faculty' => $selectedFaculty, 'major' => $maj]) }}"
                       class="group bg-white p-5 rounded-3xl border transition-all duration-200 flex flex-col justify-between
                              {{ $hasStudents 
                                 ? 'border-teal-200 hover:border-teal-500 hover:shadow-md hover:-translate-y-0.5' 
                                 : 'border-slate-200 hover:border-slate-300 hover:shadow-xs' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $mStat['with_health'] > 0 ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $mStat['total'] > 0 ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-400' }}">
                                        {{ $mStat['total'] }} Mahasiswa
                                    </span>
                                    @if($mStat['with_health'] > 0)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            {{ $mStat['with_health'] }} Lengkap
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-700 transition leading-snug">
                                {{ $maj }}
                            </h3>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="font-bold {{ $hasStudents ? 'text-teal-700' : 'text-slate-400' }} group-hover:translate-x-1 transition-transform">
                                Buka Tabel Riwayat &rarr;
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
    <!-- HALAMAN 3: TABEL MAHASISWA & RIWAYAT KESEHATAN (viewMode === 'table')  -->
    <!-- ===================================================================== -->
    @elseif($viewMode === 'table')
        <!-- Header & Navigasi Kembali Halaman 3 -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                @if($selectedFaculty)
                    <a href="{{ route('admin.health.index', ['faculty' => $selectedFaculty]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                        <span>&larr; Kembali ke Daftar Jurusan ({{ $selectedFaculty }})</span>
                    </a>
                @else
                    <a href="{{ route('admin.health.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition self-start">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                        <span>&larr; Kembali ke Daftar Fakultas</span>
                    </a>
                @endif

                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 flex-wrap">
                    <a href="{{ route('admin.health.index') }}" class="hover:text-teal-600 transition">Riwayat Kesehatan</a>
                    @if($selectedFaculty)
                        <span>/</span>
                        <a href="{{ route('admin.health.index', ['faculty' => $selectedFaculty]) }}" class="hover:text-teal-600 transition">{{ $selectedFaculty }}</a>
                    @endif
                    @if($selectedMajor)
                        <span>/</span>
                        <span class="text-teal-800 font-bold">{{ $selectedMajor }}</span>
                    @elseif($search)
                        <span>/</span>
                        <span class="text-teal-800 font-bold">Pencarian "{{ $search }}"</span>
                    @endif
                </nav>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-2 border-t border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-black flex items-center justify-center shadow-xs">3</span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            @if($selectedMajor)
                                Tabel Riwayat Kesehatan — <span class="text-teal-700">{{ $selectedMajor }}</span>
                            @else
                                Hasil Pencarian Riwayat Kesehatan Mahasiswa
                            @endif
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                            {{ $students ? $students->total() : 0 }} Mahasiswa Ditemukan
                        </span>
                    </div>
                    @if($selectedFaculty)
                        <p class="text-xs text-slate-500 mt-1">
                            Fakultas: <strong class="text-slate-800">{{ $selectedFaculty }}</strong>
                        </p>
                    @endif
                </div>

                <!-- Export Button -->
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <a href="{{ route('admin.health.export', ['faculty' => $selectedFaculty, 'major' => $selectedMajor, 'health_status' => $healthStatus]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Ekspor CSV (Data Ini)</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar Khusus Tabel Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <form method="GET" action="{{ route('admin.health.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                @if($selectedFaculty)
                    <input type="hidden" name="faculty" value="{{ $selectedFaculty }}">
                @endif
                @if($selectedMajor)
                    <input type="hidden" name="major" value="{{ $selectedMajor }}">
                @endif

                <!-- Search Input -->
                <div class="sm:col-span-6 lg:col-span-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        </span>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Cari NIM, Nama, No. HP, atau Email mahasiswa..."
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-slate-50/50 outline-hidden">
                    </div>
                </div>

                <!-- Health History Status Filter -->
                <div class="sm:col-span-4 lg:col-span-4">
                    <select name="health_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-semibold bg-white outline-hidden">
                        <option value="all">-- Semua Status Riwayat Kesehatan --</option>
                        <option value="completed" {{ $healthStatus === 'completed' ? 'selected' : '' }}>Sudah Lengkap / Mengisi</option>
                        <option value="empty" {{ $healthStatus === 'empty' ? 'selected' : '' }}>Belum Mengisi Riwayat</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition">
                        Filter
                    </button>
                    @if($search || ($healthStatus && $healthStatus !== 'all'))
                        <a href="{{ route('admin.health.index', ['faculty' => $selectedFaculty, 'major' => $selectedMajor]) }}"
                           class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABEL DATA RIWAYAT KESEHATAN MAHASISWA -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">Mahasiswa &amp; NIM</th>
                            <th class="p-3.5">Status Riwayat</th>
                            <th class="p-3.5 text-center">Gol. Darah</th>
                            <th class="p-3.5 text-center">TB / BB</th>
                            <th class="p-3.5 text-center">BMI &amp; Gizi</th>
                            <th class="p-3.5">Gaya Hidup &amp; Vaksin</th>
                            <th class="p-3.5">Alergi &amp; Penyakit</th>
                            <th class="p-3.5">Kontak Darurat</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($students as $stu)
                            @php
                                $prof = $stu->profile;
                                $hh   = $stu->healthHistory;
                                $hasHealth = $hh !== null;

                                // Student JSON representation for Alpine Modal
                                $studentJson = [
                                    'id'           => $stu->id,
                                    'name'         => $prof?->full_name ?? $stu->name,
                                    'nim'          => $prof?->nim ?? '-',
                                    'email'        => $stu->email,
                                    'phone'        => $prof?->phone ?? '-',
                                    'gender'       => $prof?->gender === 'L' ? 'Laki-laki' : ($prof?->gender === 'P' ? 'Perempuan' : '-'),
                                    'faculty'      => $prof?->faculty ?? '-',
                                    'major'        => $prof?->major ?? '-',
                                    'hasHealth'    => $hasHealth,
                                    'blood'        => $hasHealth ? ($hh->blood_type . ($hh->rhesus !== 'Tidak Tahu' ? $hh->rhesus : '')) : '-',
                                    'height'       => $hasHealth ? $hh->height_cm . ' cm' : '-',
                                    'weight'       => $hasHealth ? $hh->weight_kg . ' kg' : '-',
                                    'bmi'          => $hasHealth ? ($hh->bmi ?? '-') : '-',
                                    'bmiStatus'    => $hasHealth ? $hh->bmi_status : '-',
                                    'smoker'       => $hasHealth ? ($hh->is_smoker ? 'Perokok Aktif' : 'Bukan Perokok') : '-',
                                    'vaccine'      => $hasHealth ? $hh->vaccine_status : '-',
                                    'allergies'    => $hasHealth ? ($hh->allergies ?: 'Tidak ada') : '-',
                                    'chronic'      => $hasHealth ? ($hh->chronic_diseases ?: 'Tidak ada') : '-',
                                    'medications'  => $hasHealth ? ($hh->current_medications ?: 'Tidak ada') : '-',
                                    'surgeries'    => $hasHealth ? ($hh->past_surgeries ?: 'Tidak ada') : '-',
                                    'contactName'  => $hasHealth ? ($hh->emergency_contact_name ?: '-') : '-',
                                    'contactPhone' => $hasHealth ? ($hh->emergency_contact_phone ?: '-') : '-',
                                    'editUrl'      => route('admin.health.edit', $stu->id),
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kolom 1: Mahasiswa -->
                                <td class="p-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-linear-to-tr from-teal-500 to-emerald-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($prof?->full_name ?? $stu->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">
                                                {{ $prof?->full_name ?? $stu->name }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                NIM: {{ $prof?->nim ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $prof?->gender === 'L' ? 'Laki-laki' : ($prof?->gender === 'P' ? 'Perempuan' : '-') }}</span>
                                                <span>•</span>
                                                <span>{{ $prof?->phone ?? $stu->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom 2: Status Riwayat -->
                                <td class="p-3.5">
                                    @if($hasHealth)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                            <span>Lengkap</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            <span>Belum Isi</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Kolom 3: Gol. Darah -->
                                <td class="p-3.5 text-center">
                                    @if($hasHealth && $hh->blood_type)
                                        <span class="inline-block px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 font-black text-xs border border-rose-200">
                                            {{ $hh->blood_type }}{{ $hh->rhesus !== 'Tidak Tahu' ? $hh->rhesus : '' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 4: TB / BB -->
                                <td class="p-3.5 text-center">
                                    @if($hasHealth && ($hh->height_cm > 0 || $hh->weight_kg > 0))
                                        <div class="font-bold text-slate-800">{{ $hh->height_cm }} cm</div>
                                        <div class="text-[11px] text-slate-500">{{ $hh->weight_kg }} kg</div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 5: BMI & Gizi -->
                                <td class="p-3.5 text-center">
                                    @if($hasHealth && $hh->bmi)
                                        @php
                                            $bmiColor = match(true) {
                                                $hh->bmi < 18.5 => 'bg-amber-100 text-amber-800',
                                                $hh->bmi <= 22.9 => 'bg-emerald-100 text-emerald-800',
                                                $hh->bmi <= 24.9 => 'bg-orange-100 text-orange-800',
                                                default => 'bg-rose-100 text-rose-800',
                                            };
                                        @endphp
                                        <div class="font-black text-slate-900">{{ $hh->bmi }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $bmiColor }}">
                                            {{ $hh->bmi_status }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 6: Gaya Hidup & Vaksin -->
                                <td class="p-3.5">
                                    @if($hasHealth)
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $hh->is_smoker ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $hh->is_smoker ? '🚬 Perokok' : '🚭 Bebas Rokok' }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-1 truncate max-w-[140px]" title="{{ $hh->vaccine_status }}">
                                            💉 {{ $hh->vaccine_status ?: '-' }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 7: Alergi & Penyakit -->
                                <td class="p-3.5">
                                    @if($hasHealth)
                                        <div class="text-[11px] font-medium text-slate-700 truncate max-w-[150px]" title="Alergi: {{ $hh->allergies }}">
                                            <strong class="text-slate-500">Alergi:</strong> {{ $hh->allergies ?: 'Tidak ada' }}
                                        </div>
                                        <div class="text-[11px] font-medium text-slate-700 truncate max-w-[150px] mt-0.5" title="Kronis: {{ $hh->chronic_diseases }}">
                                            <strong class="text-slate-500">Kronis:</strong> {{ $hh->chronic_diseases ?: 'Tidak ada' }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 8: Kontak Darurat -->
                                <td class="p-3.5">
                                    @if($hasHealth && $hh->emergency_contact_name)
                                        <div class="font-bold text-slate-800 leading-tight">
                                            {{ $hh->emergency_contact_name }}
                                        </div>
                                        <div class="text-[11px] text-teal-700 font-mono mt-0.5">
                                            {{ $hh->emergency_contact_phone }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Kolom 9: Aksi -->
                                <td class="p-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Detail Pop-up -->
                                        <button type="button"
                                                @click="openDetail({{ json_encode($studentJson) }})"
                                                class="px-2.5 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs transition"
                                                title="Lihat Detail Riwayat Kesehatan">
                                            👁️ Detail
                                        </button>

                                        <!-- Tombol Edit -->
                                        <a href="{{ route('admin.health.edit', $stu->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
                                           title="Edit Riwayat Kesehatan">
                                            ✏️ Edit
                                        </a>

                                        <!-- Tombol Hapus Riwayat (jika ada) -->
                                        @if($hasHealth)
                                            <form method="POST" action="{{ route('admin.health.destroy', $stu->id) }}"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data riwayat kesehatan mahasiswa {{ addslashes($prof?->full_name ?? $stu->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition"
                                                        title="Hapus Riwayat">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-10 text-center">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                            🔍
                                        </div>
                                        <p class="text-sm font-bold text-slate-700">Tidak ada data mahasiswa ditemukan</p>
                                        <p class="text-xs text-slate-400">
                                            Silakan sesuaikan kata kunci pencarian atau reset filter.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students && $students->hasPages())
                <div class="p-4 bg-slate-50 border-t border-slate-200">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- ALPINE.JS MODAL DETAIL RIWAYAT KESEHATAN                              -->
    <!-- ===================================================================== -->
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="closeDetail()"
             x-show="detailModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="detailModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200">

                <!-- Modal Header -->
                <div class="bg-linear-to-r from-teal-700 to-emerald-800 p-6 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-black text-xl text-white">
                            🩺
                        </div>
                        <div>
                            <h3 class="text-base font-black tracking-tight" x-text="selectedStudent?.name"></h3>
                            <p class="text-xs text-teal-100 font-medium">
                                NIM: <span class="font-mono font-bold" x-text="selectedStudent?.nim"></span> • <span x-text="selectedStudent?.major"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="closeDetail()" class="text-white/80 hover:text-white transition p-1.5 rounded-xl hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 text-xs">
                    <!-- Status Banner -->
                    <div class="p-4 rounded-2xl flex items-center justify-between"
                         :class="selectedStudent?.hasHealth ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'">
                        <div class="flex items-center gap-2">
                            <span class="text-base" x-text="selectedStudent?.hasHealth ? '✅' : '⚠️'"></span>
                            <div>
                                <span class="font-bold" x-text="selectedStudent?.hasHealth ? 'Riwayat Kesehatan Lengkap' : 'Mahasiswa Belum Mengisi Riwayat Kesehatan'"></span>
                                <p class="text-[11px] opacity-80" x-text="selectedStudent?.hasHealth ? 'Data tersimpan di rekam medis SIPKESMA' : 'Harap hubungi mahasiswa atau isikan berkas kesehatan'"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase"
                              :class="selectedStudent?.hasHealth ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'"
                              x-text="selectedStudent?.hasHealth ? 'Sudah Lengkap' : 'Belum Isi'">
                        </span>
                    </div>

                    <!-- 1. Data Antropometri & Darah -->
                    <div class="space-y-2">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px] flex items-center gap-1.5">
                            <span>1. Antropometri &amp; Golongan Darah</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Golongan Darah</span>
                                <span class="text-sm font-black text-rose-600 mt-0.5 block" x-text="selectedStudent?.blood || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Tinggi Badan</span>
                                <span class="text-sm font-black text-slate-800 mt-0.5 block" x-text="selectedStudent?.height || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Berat Badan</span>
                                <span class="text-sm font-black text-slate-800 mt-0.5 block" x-text="selectedStudent?.weight || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">BMI &amp; Status</span>
                                <span class="text-sm font-black text-teal-700 mt-0.5 block" x-text="selectedStudent?.bmi || '-'"></span>
                                <span class="text-[10px] font-semibold text-slate-500 block truncate" x-text="selectedStudent?.bmiStatus || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Gaya Hidup & Vaksin -->
                    <div class="space-y-2">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px] flex items-center gap-1.5">
                            <span>2. Gaya Hidup &amp; Imunisasi</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Kebiasaan Merokok</span>
                                <span class="text-xs font-bold text-slate-800 mt-0.5 block" x-text="selectedStudent?.smoker || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Status Vaksinasi</span>
                                <span class="text-xs font-bold text-slate-800 mt-0.5 block" x-text="selectedStudent?.vaccine || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Anamnesis Medis & Alergi -->
                    <div class="space-y-2">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px] flex items-center gap-1.5">
                            <span>3. Anamnesis Medis &amp; Riwayat Alergi</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Riwayat Alergi</span>
                                <span class="text-xs font-medium text-slate-800 mt-0.5 block" x-text="selectedStudent?.allergies || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Penyakit Kronis / Bawaan</span>
                                <span class="text-xs font-medium text-slate-800 mt-0.5 block" x-text="selectedStudent?.chronic || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Obat Rutin</span>
                                <span class="text-xs font-medium text-slate-800 mt-0.5 block" x-text="selectedStudent?.medications || '-'"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-semibold block">Riwayat Operasi</span>
                                <span class="text-xs font-medium text-slate-800 mt-0.5 block" x-text="selectedStudent?.surgeries || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Kontak Darurat -->
                    <div class="space-y-2">
                        <div class="font-bold uppercase tracking-wider text-slate-400 text-[10px] flex items-center gap-1.5">
                            <span>4. Kontak Darurat</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-teal-50/50 border border-teal-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block">Nama Kontak</span>
                                <span class="text-xs font-bold text-slate-800 mt-0.5 block" x-text="selectedStudent?.contactName || '-'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 font-semibold block">No. Telepon / WhatsApp</span>
                                <span class="text-xs font-mono font-bold text-teal-800 mt-0.5 block" x-text="selectedStudent?.contactPhone || '-'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="closeDetail()"
                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                        Tutup
                    </button>

                    <a :href="selectedStudent?.editUrl"
                       class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs transition shadow-xs">
                        <span>✏️ Edit Riwayat Mahasiswa Ini</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
