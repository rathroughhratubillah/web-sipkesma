@extends('layouts.dokter')

@section('title', 'Data Mahasiswa & Riwayat Kesehatan')
@section('header_title', 'Data & Riwayat Kesehatan Mahasiswa')
@section('header_subtitle', 'Daftar mahasiswa terdaftar beserta profil identitas dan riwayat kesehatan medis')

@section('content')
<div class="space-y-5" x-data="{
    majorsMap: @js($faculties),
    selectedFaculty: @js($selectedFaculty ?? 'all'),
    selectedMajor: @js($selectedMajor ?? 'all'),
    get availableMajors() {
        return this.selectedFaculty && this.selectedFaculty !== 'all'
            ? (this.majorsMap[this.selectedFaculty] || [])
            : [];
    }
}">

    <!-- Filter Form -->
    <form method="GET" action="{{ route('dokter.patients.index') }}"
          class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-4">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Mahasiswa / Antrean</label>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Nama, NIM/NISN, Email, No. HP, atau A-001..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="all" {{ ($selectedYear ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (string)($selectedYear ?? '') === (string)$yr ? 'selected' : '' }}>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Fakultas</label>
                <select name="faculty" x-model="selectedFaculty" @change="selectedMajor = 'all'"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="all">Semua Fakultas</option>
                    @foreach(array_keys($faculties) as $facName)
                        <option value="{{ $facName }}">{{ $facName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Program Studi</label>
                <select name="major" x-model="selectedMajor"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="all">Semua Program Studi</option>
                    <template x-for="m in availableMajors" :key="m">
                        <option :value="m" x-text="m" :selected="m === selectedMajor"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-end justify-between gap-3 pt-2 border-t border-slate-100">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Riwayat Kesehatan</label>
                    <select name="health_status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="all" {{ $healthStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="completed" {{ $healthStatus === 'completed' ? 'selected' : '' }}>Sudah Isi Riwayat</option>
                        <option value="empty" {{ $healthStatus === 'empty' ? 'selected' : '' }}>Belum Isi Riwayat</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Pendaftaran</label>
                    <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="all" {{ $regStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="cleared" {{ $regStatus === 'cleared' ? 'selected' : '' }}>Lunas / Tiket Aktif</option>
                        <option value="checked_in" {{ $regStatus === 'checked_in' ? 'selected' : '' }}>Sudah Check-in</option>
                        <option value="in_progress" {{ $regStatus === 'in_progress' ? 'selected' : '' }}>Sedang Pemeriksaan</option>
                        <option value="completed" {{ $regStatus === 'completed' ? 'selected' : '' }}>Selesai Skrining</option>
                        <option value="awaiting_payment" {{ $regStatus === 'awaiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="draft" {{ $regStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Terapkan Filter
                </button>
                <a href="{{ route('dokter.patients.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Mahasiswa Terdaftar</h2>
                <p class="text-xs text-slate-400">Menampilkan {{ $registrations->total() }} mahasiswa peserta PPKMB</p>
            </div>
            <a href="{{ route('dokter.scan') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition">
                <span>Scan Barcode Tiket &rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">NIM / NISN &amp; Identitas</th>
                        <th class="p-4">Fakultas / Prodi</th>
                        <th class="p-4">Tiket &amp; Sesi</th>
                        <th class="p-4">Riwayat Kesehatan</th>
                        <th class="p-4">Alergi / Kronis</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($registrations as $reg)
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
                                <div class="text-[11px] text-slate-400">{{ $u?->email }}</div>
                                <div class="text-[11px] font-mono text-teal-700 mt-0.5">{{ $prof?->phone ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-mono font-bold text-slate-800">{{ $prof?->nim ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $prof?->gender === 'L' ? 'Laki-laki' : ($prof?->gender === 'P' ? 'Perempuan' : '-') }}
                                    @if($prof?->birth_date)
                                        &bull; {{ \Carbon\Carbon::parse($prof->birth_date)->age }} thn
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-semibold text-slate-800">{{ $prof?->faculty ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $prof?->major ?? '-' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($reg->queue_code)
                                        <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 font-mono font-bold border border-teal-200">
                                            {{ $reg->queue_code }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                                        </span>
                                    @endif
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">
                                        {{ $reg->academic_year ?? date('Y') }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-1">{{ $reg->ticket_barcode_code }}</div>
                            </td>
                            <td class="p-4">
                                @if($hh)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                            Gol. {{ $hh->blood_type }}{{ $hh->rhesus }}
                                        </span>
                                        <span class="font-mono text-slate-700">{{ $hh->height_cm }}cm / {{ $hh->weight_kg }}kg</span>
                                    </div>
                                    <div class="text-[10px] text-teal-700 font-semibold mt-0.5">
                                        BMI: {{ $hh->bmi }} ({{ $hh->bmi_status }})
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        Vaksin: {{ $hh->vaccine_status }}
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-bold">
                                        Belum Mengisi
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($hh)
                                    <div class="space-y-0.5 text-[11px] max-w-[180px]">
                                        <div class="truncate {{ $hasAllergy ? 'text-amber-800 font-bold' : 'text-slate-600' }}" title="{{ $hh->allergies }}">
                                            <strong>Alergi:</strong> {{ $hh->allergies ?: 'Tidak ada' }}
                                        </div>
                                        <div class="truncate {{ $hasChronic ? 'text-rose-700 font-bold' : 'text-slate-600' }}" title="{{ $hh->chronic_diseases }}">
                                            <strong>Kronis:</strong> {{ $hh->chronic_diseases ?: 'Tidak ada' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('dokter.patients.show', $reg) }}"
                                   class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-2xs transition whitespace-nowrap">
                                    <span>Detail Medis</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-slate-400">
                                Tidak ada data mahasiswa yang sesuai filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

