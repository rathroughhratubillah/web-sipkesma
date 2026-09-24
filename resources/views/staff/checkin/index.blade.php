@extends('layouts.staff')

@section('title', 'Meja Check-in & Scanner QR')
@section('header_title', 'Meja Check-In & Pemindai QR')

@section('content')
<div class="space-y-6" x-data="{
    manualInput: '',
    cameraActive: false,
    scanner: null,
    scanMessage: null,
    scanSuccess: false,
    
    startScanner() {
        this.cameraActive = true;
        this.$nextTick(() => {
            if (!this.scanner) {
                this.scanner = new Html5QrcodeScanner('reader', { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 } 
                }, false);
                
                this.scanner.render((decodedText) => {
                    this.processCheckin(decodedText);
                }, (error) => {
                    // scanning in progress
                });
            }
        });
    },

    stopScanner() {
        if (this.scanner) {
            this.scanner.clear().catch(error => console.error('Failed to clear scanner', error));
            this.scanner = null;
        }
        this.cameraActive = false;
    },

    async processCheckin(queryValue) {
        if (!queryValue) return;
        try {
            let response = await fetch('{{ route('staff.checkin.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ query: queryValue })
            });

            let data = await response.json();
            this.scanSuccess = data.success;
            this.scanMessage = data.message;
            this.manualInput = '';

            // Play sound effect using Web Audio API
            let ctx = new (window.AudioContext || window.webkitAudioContext)();
            let osc = ctx.createOscillator();
            let gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = data.success ? 600 : 250;
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.2);

            if (data.success) {
                setTimeout(() => window.location.reload(), 1500);
            }
        } catch (e) {
            this.scanSuccess = false;
            this.scanMessage = 'Gagal memproses check-in. Pastikan koneksi server aktif.';
        }
    }
}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Scanner & Manual Input (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pemindai QR & Check-in Lokasi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pindai QR pada tiket antrean atau input manual nomor antrean / NISN.</p>
            </div>

            <!-- Toast / Message Box -->
            <div x-show="scanMessage" 
                 x-cloak
                 class="p-4 rounded-2xl text-xs font-semibold"
                 :class="scanSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                <div class="flex items-center gap-2">
                    <span x-text="scanSuccess ? '✓' : '✕'"></span>
                    <span x-text="scanMessage"></span>
                </div>
            </div>

            <!-- Camera Scanner Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kamera Scanner</span>
                    <button type="button" 
                            @click="cameraActive ? stopScanner() : startScanner()"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            :class="cameraActive ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-teal-600 text-white hover:bg-teal-700 shadow-xs'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /></svg>
                        <span x-text="cameraActive ? 'Matikan Kamera' : 'Buka Kamera Pemindai'"></span>
                    </button>
                </div>

                <div x-show="cameraActive" class="rounded-2xl overflow-hidden border border-teal-200 bg-slate-900 p-2">
                    <div id="reader" class="w-full"></div>
                </div>

                <div x-show="!cameraActive" class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                    <svg class="w-10 h-10 text-slate-400 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" /></svg>
                    <p class="text-xs text-slate-500">Klik "Buka Kamera Pemindai" untuk scan langsung dari kamera perangkat atau webcam.</p>
                </div>
            </div>

            <!-- Manual Input Form -->
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Input Manual (Nomor Antrean / NISN)</span>
                
                <div class="flex items-center gap-2">
                    <input type="text" x-model="manualInput" 
                           @keydown.enter.prevent="processCheckin(manualInput)"
                           placeholder="Contoh: A-001 atau 2026101001"
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 font-mono text-sm outline-hidden uppercase">
                    <button type="button" 
                            @click="processCheckin(manualInput)"
                            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition">
                        Check-in
                    </button>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-teal-50 text-teal-800 text-xs flex items-center justify-between">
                <span>Siap Check-in: <strong>{{ $stats['ready_to_checkin'] }} peserta</strong></span>
                <a href="{{ route('staff.queue.display') }}" class="font-bold underline">Buka Monitor Antrean &rarr;</a>
            </div>

        </div>

        <!-- Right: Recent Check-in Attendance Table (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Daftar Kehadiran Hari Ini</h3>
                    <p class="text-xs text-slate-500">Peserta yang telah tiba dan terekam di meja check-in</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
                    {{ $stats['total_checked_in_today'] }} Hadir
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">No Antrean</th>
                            <th class="p-3">Mahasiswa</th>
                            <th class="p-3">Sesi Jadwal</th>
                            <th class="p-3">Jam Check-in</th>
                            <th class="p-3 text-right">Status Medis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($todayCheckins as $check)
                            @php
                                $u = $check->user;
                                $prof = $u?->profile;
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3">
                                    <span class="font-bold font-mono text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md text-sm">
                                        {{ $check->queue_code }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-900">{{ $prof?->full_name ?: $u?->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $prof?->nim }}</div>
                                </td>
                                <td class="p-3 text-slate-600">
                                    {{ $check->testSession?->session_name ?: 'Sesi Reguler' }}
                                </td>
                                <td class="p-3 font-semibold text-slate-700 font-mono">
                                    {{ $check->checked_in_at?->format('H:i:s') }} WIB
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('staff.station.index', 'urin') }}" class="px-3 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] transition">
                                        Periksa Stasiun &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    Belum ada peserta yang check-in hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
