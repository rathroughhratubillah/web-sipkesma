@extends('layouts.student')

@section('title', 'Tiket Antrean Skrining Medis')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tiket Antrean Skrining</h1>
            <p class="text-sm text-slate-500 mt-1">Kartu resmi antrean dan bukti pembayaran skrining kesehatan PPKMB UINSSC.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 8 dari 11
        </span>
    </div>

    @if($isUnlocked)
        <!-- Physical Boarding Pass Style Ticket UI -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden relative">
            
            <!-- Top Color Bar -->
            <div class="bg-gradient-to-r from-teal-700 via-teal-600 to-emerald-600 text-white px-6 sm:px-8 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-tight">SIPKESMA UINSSC</div>
                        <div class="text-[11px] text-teal-100 uppercase tracking-wider font-semibold">Tiket Resmi Skrining Medis Mahasiswa Baru</div>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-400 text-slate-900 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        <span>{{ in_array($registration->status, ['checked_in', 'in_progress', 'completed']) ? 'Sudah Check-In' : 'Lunas & Siap Hadir' }}</span>
                    </span>
                </div>
            </div>

            <!-- Ticket Body with 2-Column Perforated Ticket Layout -->
            <div class="grid grid-cols-1 md:grid-cols-12 relative">
                
                <!-- Main Body (7 Columns on Desktop) -->
                <div class="md:col-span-8 p-6 sm:p-8 space-y-6">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider block">Kartu Bukti Peserta</span>
                            <h2 class="text-base font-bold text-slate-800">Skrining Kesehatan Terpadu PPKMB</h2>
                        </div>
                        <span class="sm:hidden inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Lunas & Valid
                        </span>
                    </div>

                    <!-- Participant Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Nama Lengkap</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $user->profile->full_name ?? $user->name }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block font-medium">NIM / ID Mahasiswa</span>
                            <span class="font-bold text-slate-900 font-mono text-sm mt-0.5 block">{{ $user->profile->nim ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block font-medium">Fakultas / Program Studi</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $user->profile->faculty ?? '-' }} &bull; {{ $user->profile->major ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block font-medium">Tanggal Pelaksanaan</span>
                            <span class="font-bold text-slate-900 mt-0.5 block">
                                @if($registration->testSession)
                                    {{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                @else
                                    Sesuai Jadwal
                                @endif
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block font-medium">Sesi & Jam Kedatangan</span>
                            <span class="font-bold text-teal-700 mt-0.5 block">
                                @if($registration->testSession)
                                    {{ substr($registration->testSession->start_time, 0, 5) }} - {{ substr($registration->testSession->end_time, 0, 5) }} WIB
                                @else
                                    Sesi Terjadwal
                                @endif
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block font-medium">Lokasi Skrining</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">Klinik Kampus - Gedung Layanan Terpadu</span>
                        </div>
                    </div>

                    <!-- 3 Stasiun Skrining Checklist -->
                    <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-100">
                        <span class="text-[11px] font-bold text-teal-900 uppercase tracking-wider block mb-2">Rangkaian 3 Stasiun Skrining</span>
                        <div class="grid grid-cols-3 gap-2 text-xs font-semibold text-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded border-2 border-teal-600 bg-white inline-block shrink-0"></span>
                                <span>1. Tes Buta Warna</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded border-2 border-teal-600 bg-white inline-block shrink-0"></span>
                                <span>2. Tes NAPZA</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded border-2 border-teal-600 bg-white inline-block shrink-0"></span>
                                <span>3. Tes Fisik</span>
                            </div>
                        </div>
                    </div>

                    <!-- Real Scannable 1D Barcode Code 128B -->
                    <div class="pt-3 flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/80 shadow-2xs max-w-full overflow-x-auto flex items-center justify-center">
                            @if(!empty($barcodeSvg))
                                {!! $barcodeSvg !!}
                            @endif
                        </div>
                        <div class="text-[11px] font-mono font-bold tracking-widest text-slate-700 mt-2 select-all">
                            {{ $barcodeCode ?? $registration->ticket_barcode_code }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">
                            Scan barcode ini oleh Dokter / Petugas Klinik saat pemeriksaan di tempat
                        </div>
                    </div>

                </div>

                <!-- Perforated Tear-off Stub (4 Columns on Desktop) -->
                <div class="md:col-span-4 bg-slate-50/80 p-6 sm:p-8 flex flex-col justify-between items-center text-center border-t md:border-t-0 md:border-l-2 border-dashed border-slate-300 relative">
                    
                    <!-- Decorative Semicircular Ticket Cutouts (Desktop) -->
                    <div class="hidden md:block absolute -top-3.5 -left-3.5 w-7 h-7 bg-[#f6fbfa] rounded-full border-b border-slate-200"></div>
                    <div class="hidden md:block absolute -bottom-3.5 -left-3.5 w-7 h-7 bg-[#f6fbfa] rounded-full border-t border-slate-200"></div>

                    <div class="w-full space-y-4">
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-teal-800">Nomor Antrean Anda</span>
                            <div class="mt-2 py-4 px-3 rounded-2xl bg-white border-2 border-teal-600 shadow-inner">
                                <div class="text-5xl font-black text-teal-900 font-mono tracking-tight">
                                    {{ $registration->queue_code ?? 'A-001' }}
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-1">Urutan Panggilan</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-white border border-slate-200 text-left text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Sesi:</span>
                                <span class="font-bold text-slate-800">{{ $registration->testSession->session_name ?? 'Sesi 1' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pintu:</span>
                                <span class="font-bold text-teal-700">Meja Layanan A</span>
                            </div>
                        </div>

                        <!-- Stamp Badge -->
                        <div class="inline-block transform -rotate-3 border-2 border-emerald-600 text-emerald-700 rounded-xl px-4 py-1.5 font-black text-xs uppercase tracking-wider shadow-xs">
                            &#10003; SIPKESMA VERIFIED
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 mt-4 leading-relaxed">
                        Tunjukkan nomor tiket ini ke petugas meja check-in klinik saat tiba di lokasi.
                    </p>

                </div>

            </div>

            <!-- Footer Action Buttons -->
            <div class="bg-slate-50 border-t border-slate-100 p-4 sm:px-8 flex flex-wrap items-center justify-between gap-3">
                <button onclick="window.print()" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-white text-slate-700 text-xs font-bold transition flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.042-.036-2.138.566-3.003L12 3.75l4.714 7.076c.602.865.806 1.961.566 3.003m-9.428 0a4.5 4.5 0 008.856 0m-8.856 0c.24 1.042.036 2.138-.566 3.003M12 20.25v-6.75" /></svg>
                    <span>Cetak Tiket Langsung</span>
                </button>

                <a href="{{ route('student.ticket.download') }}" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Unduh PDF Resmi</span>
                </a>
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
                Tiket antrean resmi dan nomor urut pemeriksaan akan diterbitkan setelah bukti transfer pembayaran Anda diverifikasi dan disetujui oleh tim admin / petugas skrining.
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
