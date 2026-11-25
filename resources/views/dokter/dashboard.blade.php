@extends('layouts.dokter')

@section('title', 'Dashboard Dokter & Tim Kesehatan')
@section('header_title', 'Dashboard Dokter & Tim Kesehatan')
@section('header_subtitle', 'Pemantauan riwayat kesehatan, identitas mahasiswa, dan pemindaian barcode tiket di tempat')

@section('content')
<div class="space-y-6">

    <!-- Filter Tahun Akademik -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base shrink-0">
                🩺
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900">Ringkasan Rekam Medis PPKMB</h3>
                    @if(($selectedYear ?? 'all') !== 'all')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                            {{ $selectedYear }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                            Semua Tahun
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">Data riwayat kesehatan, antropometri, alergi, dan status antrean mahasiswa</p>
            </div>
        </div>

        <form method="GET" action="{{ route('dokter.dashboard') }}" class="flex items-center gap-2 shrink-0">
            <label for="dokter-year-select" class="text-xs font-semibold text-slate-600">Pilih Tahun:</label>
            <select id="dokter-year-select" name="year" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                <option value="all" {{ ($selectedYear ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ (string)($selectedYear ?? '') === (string)$yr ? 'selected' : '' }}>
                        {{ $yr }}
                    </option>
                @endforeach
            </select>
            @if(($selectedYear ?? 'all') !== 'all')
                <a href="{{ route('dokter.dashboard') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    ✕ Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Quick Scan Barcode Banner -->
    <div class="bg-linear-to-r from-teal-700 via-teal-600 to-emerald-700 rounded-3xl p-6 text-white shadow-lg shadow-teal-900/10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
            <div class="lg:col-span-6 space-y-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                    Scan Tiket Di Tempat
                </span>
                <h2 class="text-xl font-black tracking-tight">Scan Barcode Tiket Mahasiswa</h2>
                <p class="text-xs text-teal-100 leading-relaxed">
                    Arahkan barcode scanner pada kode tiket (<span class="font-mono font-bold text-white">SPK-...</span>), ketik nomor antrean (<span class="font-mono font-bold text-white">A-001</span>), atau NIM untuk memunculkan data diri &amp; riwayat kesehatan mahasiswa secara instan.
                </p>
            </div>

            <div class="lg:col-span-6">
                <form method="GET" action="{{ route('dokter.scan') }}" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" name="code" autofocus
                           placeholder="Scan / ketik kode barcode tiket, No. Antrean, atau NIM..."
                           class="flex-1 px-4 py-3 rounded-xl bg-white text-slate-900 placeholder:text-slate-400 text-xs font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-300 shadow-xs">
                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition cursor-pointer whitespace-nowrap">
                            Cek Barcode &rarr;
                        </button>
                        <a href="{{ route('dokter.scan') }}"
                           class="px-4 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/25 font-bold text-xs transition flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /></svg>
                            <span>Kamera</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('dokter.patients.index', ['year' => $selectedYear]) }}"
           class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-teal-300 transition flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shrink-0">🎓</div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['total_students'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Mahasiswa Terdaftar</div>
            </div>
        </a>

        <a href="{{ route('dokter.patients.index', ['year' => $selectedYear, 'health_status' => 'completed']) }}"
           class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-emerald-300 transition flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">📋</div>
            <div>
                <div class="text-2xl font-black text-emerald-700 leading-tight">{{ $stats['with_health'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Riwayat Kesehatan Lengkap ({{ $stats['completion_rate'] }}%)</div>
            </div>
        </a>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">🚨</div>
            <div>
                <div class="text-2xl font-black text-rose-600 leading-tight">{{ $stats['flagged_medical'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Catatan Alergi / Kronis</div>
            </div>
        </div>

        <a href="{{ route('dokter.patients.index', ['year' => $selectedYear, 'health_status' => 'empty']) }}"
           class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-amber-300 transition flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">⏳</div>
            <div>
                <div class="text-2xl font-black text-amber-600 leading-tight">{{ $stats['ready_for_exam'] }}</div>
                <div class="text-xs text-slate-500 font-medium">Antrean Siap / Periksa</div>
            </div>
        </a>
    </div>

    <!-- Recent Registered Students + Health History Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Data Mahasiswa &amp; Riwayat Kesehatan Terbaru</h3>
                <p class="text-xs text-slate-400">Klik detail untuk melihat profil lengkap &amp; anamnesis medis peserta</p>
            </div>
            <a href="{{ route('dokter.patients.index', ['year' => $selectedYear]) }}"
               class="text-xs font-bold text-teal-700 hover:text-teal-800 hover:underline">
                Lihat Semua Data Mahasiswa &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Fakultas &amp; Prodi</th>
                        <th class="p-4">Antrean / Jadwal</th>
                        <th class="p-4">Antropometri</th>
                        <th class="p-4">Alergi &amp; Kondisi Khusus</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentPatients as $reg)
                        @php
                            $u = $reg->user;
                            $prof = $u?->profile;
                            $hh = $u?->healthHistory;
                            $hasAllergy = $hh && !in_array(strtolower(trim((string) $hh->allergies)), ['', '-', 'tidak ada', 'tidak', 'none', 'nihil'], true);
                            $hasChronic = $hh && !in_array(strtolower(trim((string) $hh->chronic_diseases)), ['', '-', 'tidak ada', 'tidak', 'none', 'nihil'], true);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $prof?->full_name ?: $u?->name }}</div>
                                <div class="text-[11px] font-mono text-slate-500">{{ $prof?->nim ?? 'Belum isi NIM' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $u?->email }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800">{{ $prof?->faculty ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $prof?->major ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    @if($reg->queue_code)
                                        <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 font-mono font-bold border border-teal-200">
                                            {{ $reg->queue_code }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-semibold">
                                            Belum Terbit
                                        </span>
                                    @endif
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">
                                        {{ $reg->academic_year ?? date('Y') }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">{{ $reg->testSession?->session_name ?? 'Belum pilih jadwal' }}</div>
                            </td>
                            <td class="p-4">
                                @if($hh)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                            Gol. {{ $hh->blood_type }}{{ $hh->rhesus }}
                                        </span>
                                        <span class="font-mono text-slate-700">{{ $hh->height_cm }}cm / {{ $hh->weight_kg }}kg</span>
                                    </div>
                                    <div class="text-[10px] text-teal-700 font-semibold mt-0.5">BMI: {{ $hh->bmi }} ({{ $hh->bmi_status }})</div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold">Belum Isi Riwayat</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($hh)
                                    <div class="space-y-0.5 text-[11px]">
                                        <div class="{{ $hasAllergy ? 'text-amber-800 font-bold' : 'text-slate-600' }}">
                                            Alergi: {{ $hh->allergies ?: 'Tidak ada' }}
                                        </div>
                                        <div class="{{ $hasChronic ? 'text-rose-700 font-bold' : 'text-slate-600' }}">
                                            Kronis: {{ $hh->chronic_diseases ?: 'Tidak ada' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('dokter.patients.show', $reg) }}"
                                   class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-2xs transition">
                                    <span>Lihat Rekam Medis</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Belum ada data mahasiswa terdaftar pada tahun ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

