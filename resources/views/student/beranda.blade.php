@extends('layouts.student')

@section('title', 'Beranda Peserta')

@section('content')
<div class="space-y-6">
    <!-- Greeting Header matching Screenshot 5 -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Selamat datang, {{ $user->name }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Ikuti tahapan di bawah agar skrining kesehatan PPKMB Anda tuntas.
        </p>
    </div>

    <!-- Main Grid matching Screenshot 5 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Tahapan Skrining (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-6">
                Tahapan Skrining
            </h2>

            <!-- Stepper List -->
            <div class="space-y-5">
                @foreach($steps as $stepId => $step)
                    @php
                        $isCurrent = ($stepId === $currentStepId);
                        $isDone = $step['is_completed'];
                    @endphp

                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <!-- Indicator Circle -->
                            <div class="shrink-0 flex items-center justify-center">
                                @if($isDone)
                                    <!-- Completed Check Icon -->
                                    <div class="w-7 h-7 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                @elseif($isCurrent)
                                    <!-- Active Radio with dot -->
                                    <div class="w-7 h-7 rounded-full border-2 border-teal-600 flex items-center justify-center">
                                        <div class="w-2.5 h-2.5 rounded-full bg-teal-600"></div>
                                    </div>
                                @else
                                    <!-- Upcoming Muted Dot -->
                                    <div class="w-7 h-7 flex items-center justify-center">
                                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                    </div>
                                @endif
                            </div>

                            <!-- Step Title -->
                            <div>
                                <span class="text-sm font-semibold {{ $isCurrent ? 'text-slate-900 font-bold' : ($isDone ? 'text-slate-800' : 'text-slate-400') }}">
                                    {{ $step['name'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Pill Badge for active step -->
                        <div>
                            @if($isCurrent)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-teal-600 text-white">
                                    Tahap ini
                                </span>
                            @elseif($isDone && $step['route'])
                                <a href="{{ $step['route'] }}" class="text-xs font-medium text-slate-400 hover:text-teal-600 transition">
                                    Lihat
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Column: Langkah Berikutnya & Ringkasan (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card 1: Langkah berikutnya matching Screenshot 5 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900">
                    Langkah berikutnya
                </h3>
                
                <p class="text-sm text-slate-600">
                    {{ $nextStep['name'] }}
                </p>

                @if($nextStep['route'])
                    <a href="{{ $nextStep['route'] }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-3 px-5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition">
                        <span>Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <div class="p-3 rounded-xl bg-teal-50 text-teal-800 text-xs font-medium">
                        Anda telah menyelesaikan seluruh tahapan skrining PPKMB!
                    </div>
                @endif
            </div>

            <!-- Card 2: Ringkasan matching Screenshot 5 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-50">
                    Ringkasan
                </h3>

                <dl class="space-y-4 text-xs sm:text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500 font-medium">Status</dt>
                        <dd class="font-bold text-slate-800 text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $registration->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                   ($registration->status === 'cleared' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-slate-100 text-slate-700') }}">
                                {{ $statusText }}
                            </span>
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500 font-medium">Nomor urut</dt>
                        <dd class="font-bold {{ $registration->queue_code ? 'text-teal-700 text-base font-mono' : 'text-slate-400' }} text-right">
                            {{ $registration->queue_code ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500 font-medium shrink-0">Jadwal</dt>
                        <dd class="font-semibold text-slate-800 text-right">
                            @if($registration->testSession)
                                {{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }} &middot; {{ $registration->testSession->session_name }}
                            @else
                                <span class="text-slate-400">Belum memilih sesi</span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500 font-medium shrink-0">Lokasi</dt>
                        <dd class="font-semibold text-slate-800 text-right">
                            Klinik Kampus - Gedung A
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>
</div>
@endsection
