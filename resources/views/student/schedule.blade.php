@extends('layouts.student')

@section('title', 'Pilih Jadwal Skrining')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pilih Jadwal Sesi Tes</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih tanggal dan jam kedatangan skrining sesuai ketersediaan kuota lokasi.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 4 dari 11
        </span>
    </div>

    <!-- Sesi List Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6"
         x-data="{ selectedSession: '{{ old('test_session_id', $registration->test_session_id ?? '') }}' }">
        
        <form method="POST" action="{{ route('student.schedule.select') }}" class="space-y-6">
            @csrf

            <div class="space-y-3">
                @forelse($sessions as $session)
                    @php
                        $isFull = $session->remaining_quota <= 0;
                        $isSelected = ($registration->test_session_id == $session->id);
                    @endphp

                    <label class="block relative p-4 sm:p-5 rounded-2xl border-2 transition cursor-pointer"
                           :class="selectedSession == {{ $session->id }} ? 'border-teal-600 bg-teal-50/40 shadow-sm' : 'border-slate-200 hover:border-teal-300 {{ $isFull ? 'opacity-60 cursor-not-allowed bg-slate-50' : '' }}'">
                        
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <input type="radio" name="test_session_id" value="{{ $session->id }}"
                                       x-model="selectedSession"
                                       {{ $isFull ? 'disabled' : '' }}
                                       required
                                       class="mt-1 text-teal-600 focus:ring-teal-500">
                                
                                <div>
                                    <div class="text-sm font-bold text-slate-900">
                                        {{ \Carbon\Carbon::parse($session->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                    </div>
                                    <div class="text-xs font-semibold text-teal-700 mt-0.5">
                                        {{ $session->session_name }} ({{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB)
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                        <span>Lokasi: Gedung Poliklinik Kampus Induk UINSSC</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quota Indicator -->
                            <div class="text-right shrink-0">
                                @if($isFull)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Kuota Penuh
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Sisa: {{ $session->remaining_quota }} slot
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-1">Kapasitas: {{ $session->quota }} mhs</div>
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200 text-slate-500 text-sm">
                        Belum ada jadwal sesi skrining aktif yang dibuka oleh panitia.
                    </div>
                @endforelse
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('student.health_history') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    &larr; Riwayat Kesehatan
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                    <span>Pilih & Tinjau Konfirmasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
