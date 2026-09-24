@extends('layouts.student')

@section('title', 'Tiket Antrean & QR Check-in')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tiket Antrean & QR Code</h1>
            <p class="text-sm text-slate-500 mt-1">Tunjukkan kartu ini kepada petugas medis di meja check-in klinik kampus.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 8 dari 11
        </span>
    </div>

    @if($isUnlocked)
        <!-- Digital Boarding Card Style Ticket -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden relative">
            
            <!-- Ticket Header -->
            <div class="bg-gradient-to-r from-teal-700 to-teal-600 text-white p-6 sm:p-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-tight">Sipkesma UINSSC</div>
                        <div class="text-[11px] text-teal-100 uppercase tracking-wider">Tiket Resmi Skrining PPKMB</div>
                    </div>
                </div>

                <div class="text-right">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-400 text-slate-900">
                        {{ in_array($registration->status, ['checked_in', 'in_progress', 'completed']) ? 'Sudah Check-In' : 'Siap Hadir' }}
                    </span>
                </div>
            </div>

            <!-- Big Number & QR Body -->
            <div class="p-6 sm:p-8 space-y-6">
                
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-6 rounded-2xl bg-teal-50/50 border border-teal-100">
                    <!-- Number -->
                    <div class="text-center sm:text-left space-y-1">
                        <span class="text-xs font-bold text-teal-800 uppercase tracking-widest">Nomor Antrean Anda</span>
                        <div class="text-5xl sm:text-6xl font-black text-teal-900 font-mono tracking-tight">
                            {{ $registration->queue_code ?? 'A-001' }}
                        </div>
                        <p class="text-xs text-slate-500">Gunakan nomor ini saat pemanggilan stasiun medis.</p>
                    </div>

                    <!-- QR Code -->
                    <div class="shrink-0 p-3 bg-white rounded-2xl border border-teal-200 shadow-sm flex flex-col items-center">
                        @if($qrSvg)
                            <div class="w-36 h-36 flex items-center justify-center">
                                {!! $qrSvg !!}
                            </div>
                        @else
                            <div class="w-36 h-36 bg-slate-100 flex items-center justify-center text-xs text-slate-400">
                                QR Code
                            </div>
                        @endif
                        <span class="text-[10px] font-mono text-slate-500 mt-1.5">{{ $registration->queue_code }} &middot; NISN {{ $user->profile->nim }}</span>
                    </div>
                </div>

                <!-- Participant & Session Details -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs pt-2">
                    <div>
                        <span class="text-slate-400 block font-medium">Nama Mahasiswa</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $user->profile->full_name }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">NISN</span>
                        <span class="font-bold text-slate-900 font-mono text-sm">{{ $user->profile->nim }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Fakultas / Prodi</span>
                        <span class="font-semibold text-slate-800">{{ $user->profile->faculty }} &middot; {{ $user->profile->major }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Tanggal Tes</span>
                        <span class="font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('D MMMM Y') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Sesi & Jam</span>
                        <span class="font-bold text-teal-700">
                            {{ substr($registration->testSession->start_time, 0, 5) }} - {{ substr($registration->testSession->end_time, 0, 5) }} WIB
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Lokasi</span>
                        <span class="font-bold text-slate-900">Klinik Kampus - Gedung A</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="p-4 rounded-2xl bg-slate-50 text-slate-600 text-xs space-y-1.5 border border-slate-200/80">
                    <span class="font-bold text-slate-800 block">Petunjuk Hadir Skrining:</span>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li>Hadir paling lambat 15 menit sebelum sesi dimulai.</li>
                        <li>Tunjukkan QR Code ini di meja check-in panitia.</li>
                        <li>Pastikan kondisi tubuh fit dan cukup istirahat sebelum tes urin dan NAPZA.</li>
                    </ul>
                </div>

                <!-- Print / Download Button -->
                <div class="pt-2 flex flex-wrap items-center justify-between gap-3">
                    <button onclick="window.print()" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.042-.036-2.138.566-3.003L12 3.75l4.714 7.076c.602.865.806 1.961.566 3.003m-9.428 0a4.5 4.5 0 008.856 0m-8.856 0c.24 1.042.036 2.138-.566 3.003M12 20.25v-6.75" /></svg>
                        <span>Cetak Layar</span>
                    </button>

                    <a href="{{ route('student.ticket.download') }}" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Unduh PDF Tiket Antrean</span>
                    </a>
                </div>

            </div>
        </div>
    @else
        <!-- Locked State Card -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-4 shadow-sm">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Tiket Antrean Belum Terbuka</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                Tiket antrean resmi dan kode QR check-in akan diterbitkan secara otomatis setelah pembayaran Anda diverifikasi dan disetujui oleh panitia medis.
            </p>
            <div class="pt-2">
                <a href="{{ route('student.payment') }}" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition inline-block">
                    Ke Halaman Pembayaran &rarr;
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
