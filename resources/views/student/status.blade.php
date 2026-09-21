@extends('layouts.student')

@section('title', 'Status Pemeriksaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pelacak Status Pemeriksaan</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau progres kehadiran check-in dan hasil pemeriksaan 3 stasiun tes secara langsung.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 9 & 10 dari 11
        </span>
    </div>

    <!-- Check-in Status Card -->
    <div class="p-6 rounded-3xl bg-white border border-slate-100 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $registration->checked_in_at ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Status Kehadiran Check-In</h3>
                @if($registration->checked_in_at)
                    <p class="text-xs text-emerald-700 font-medium">Telah hadir di lokasi pada {{ $registration->checked_in_at->format('d/m/Y H:i') }} WIB</p>
                @else
                    <p class="text-xs text-slate-500">Belum check-in di klinik kampus. Tunjukkan QR Code pada tiket Anda saat tiba.</p>
                @endif
            </div>
        </div>

        <div>
            @if($registration->checked_in_at)
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Hadir di Lokasi
                </span>
            @else
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    Menunggu Hadir
                </span>
            @endif
        </div>
    </div>

    <!-- 3 Stasiun Examination Cards -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider px-1">Progres 3 Stasiun Medis</h2>

        @foreach($stations as $key => $st)
            @php
                $result = $registration->getStationResult($key);
                $isPass = ($result && $result->status === 'pass');
                $isFollowup = ($result && $result->status === 'followup');
                $isFail = ($result && $result->status === 'fail');
            @endphp

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $st['title'] }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $st['desc'] }}</p>
                    </div>

                    <!-- Badge Status -->
                    <div class="shrink-0">
                        @if($isPass)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Layak (Pass)
                            </span>
                        @elseif($isFollowup)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Tindak Lanjut
                            </span>
                        @elseif($isFail)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Tidak Layak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Belum Diperiksa
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Examination notes if evaluated -->
                @if($result)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                        @if($result->notes)
                            <div>
                                <span class="text-slate-400 font-medium block">Catatan Dokter / Hasil:</span>
                                <p class="text-slate-800 font-semibold mt-0.5">{{ $result->notes }}</p>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-200/60">
                            <span>Pemeriksa: <strong class="text-slate-700">{{ $result->examiner_name ?: 'Tim Medis Sipkesma' }}</strong></span>
                            <span>Waktu: {{ $result->examined_at?->format('d/m/Y H:i') }} WIB</span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Ready for Certificate Banner -->
    @if($registration->status === 'completed')
        <div class="p-6 rounded-3xl bg-teal-600 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-lg shadow-teal-700/20">
            <div>
                <h3 class="text-lg font-bold">Selamat! Seluruh Pemeriksaan Selesai</h3>
                <p class="text-xs text-teal-100 mt-0.5">Anda dinyatakan lulus skrining kesehatan PPKMB. Sertifikat resmi telah diterbitkan.</p>
            </div>
            <a href="{{ route('student.certificate') }}" class="px-6 py-2.5 rounded-xl bg-white text-teal-800 hover:bg-teal-50 font-bold text-xs shrink-0 transition">
                Unduh Sertifikat Kesehatan &rarr;
            </a>
        </div>
    @endif
</div>
@endsection
