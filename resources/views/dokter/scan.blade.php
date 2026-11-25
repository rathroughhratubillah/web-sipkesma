@extends('layouts.dokter')

@section('title', 'Scan Barcode Tiket Mahasiswa')
@section('header_title', 'Scan Barcode Tiket Mahasiswa')
@section('header_subtitle', 'Pindai barcode 1D pada tiket antrean untuk menampilkan data diri & riwayat kesehatan secara langsung')

@section('content')
<div class="space-y-6" x-data="{
    codeInput: @js($code ?? ''),
    record: @js($scannedPayload),
    scanError: @js($scanError),
    scanSuccessMsg: @js($scannedPayload ? 'Data diri & riwayat kesehatan peserta berhasil dimuat.' : null),
    loading: false,
    cameraActive: false,
    scanner: null,
    lastScanned: '',
    lastScanTime: 0,

    init() {
        this.$nextTick(() => {
            this.$refs.barcodeInput?.focus();
        });
    },

    playBeep(isSuccess) {
        try {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            const ctx = new Ctx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = isSuccess ? 720 : 240;
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.16);
        } catch (e) {}
    },

    startScanner() {
        this.cameraActive = true;
        this.$nextTick(() => {
            if (!this.scanner && typeof window.Html5QrcodeScanner !== 'undefined') {
                const formats = typeof window.Html5QrcodeSupportedFormats !== 'undefined'
                    ? [
                        window.Html5QrcodeSupportedFormats.CODE_128,
                        window.Html5QrcodeSupportedFormats.CODE_39,
                        window.Html5QrcodeSupportedFormats.CODE_93,
                        window.Html5QrcodeSupportedFormats.EAN_13,
                        window.Html5QrcodeSupportedFormats.QR_CODE
                    ]
                    : undefined;

                const config = {
                    fps: 12,
                    qrbox: { width: 300, height: 150 },
                    rememberLastUsedCamera: true
                };
                if (formats) {
                    config.formatsToSupport = formats;
                }

                this.scanner = new window.Html5QrcodeScanner('dokter-barcode-reader', config, false);
                this.scanner.render((decodedText) => {
                    const now = Date.now();
                    if (decodedText === this.lastScanned && (now - this.lastScanTime) < 2500) {
                        return;
                    }
                    this.lastScanned = decodedText;
                    this.lastScanTime = now;
                    this.codeInput = decodedText;
                    this.lookupCode(decodedText);
                }, () => {});
            }
        });
    },

    stopScanner() {
        if (this.scanner) {
            this.scanner.clear().catch(() => {});
            this.scanner = null;
        }
        this.cameraActive = false;
    },

    async lookupCode(rawCode) {
        const clean = (rawCode || '').trim();
        if (!clean) return;

        this.loading = true;
        this.scanError = null;
        this.scanSuccessMsg = null;

        try {
            const response = await fetch('{{ route('dokter.scan.lookup') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ code: clean })
            });

            const json = await response.json();
            if (response.ok && json.success) {
                this.record = json.data;
                this.scanSuccessMsg = json.message;
                this.playBeep(true);
                const url = new URL(window.location.href);
                url.searchParams.set('code', clean);
                window.history.replaceState({}, '', url.toString());
            } else {
                this.record = null;
                this.scanError = json.message || 'Data peserta tidak ditemukan.';
                this.playBeep(false);
            }
        } catch (e) {
            this.scanError = 'Gagal menghubungi server pemindaian. Silakan coba lagi.';
            this.playBeep(false);
        } finally {
            this.loading = false;
            this.codeInput = '';
            this.$nextTick(() => this.$refs.barcodeInput?.focus());
        }
    },

    async confirmOnSiteCheckin(url) {
        if (!url) return;
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
            const json = await response.json();
            if (json.data) {
                this.record = json.data;
            }
            if (json.success) {
                this.scanSuccessMsg = json.message;
                this.scanError = null;
            } else {
                this.scanError = json.message;
            }
        } catch (e) {
            this.scanError = 'Gagal memproses check-in di tempat.';
        }
    },

    resetScan() {
        this.record = null;
        this.scanError = null;
        this.scanSuccessMsg = null;
        this.codeInput = '';
        const url = new URL(window.location.href);
        url.searchParams.delete('code');
        window.history.replaceState({}, '', url.toString());
        this.$nextTick(() => this.$refs.barcodeInput?.focus());
    }
}">

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Barcode Gun Input + Camera Scanner + Quick Verified List (4 cols) -->
        <div class="xl:col-span-4 space-y-5 print:hidden">

            <!-- Scanner Input Box -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase tracking-wider mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Scanner Siap Di Tempat
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Pemindai Barcode Tiket</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Scan barcode tiket (<span class="font-mono font-semibold">SPK-...</span>) memakai alat scanner atau kamera, maupun ketik No. Antrean / NIM.
                    </p>
                </div>

                <!-- Feedback Toast -->
                <div x-show="scanSuccessMsg" x-cloak
                     class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <span class="shrink-0">✓</span>
                    <span x-text="scanSuccessMsg"></span>
                </div>

                <div x-show="scanError" x-cloak
                     class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                    <span class="shrink-0">✕</span>
                    <span x-text="scanError"></span>
                </div>

                <!-- USB / Hardware Scanner & Manual Input -->
                <form @submit.prevent="lookupCode(codeInput)" class="space-y-2.5">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        Kode Barcode / Antrean / NIM
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-ref="barcodeInput"
                               x-model="codeInput"
                               placeholder="Scan SPK-2026-PPKMB-A-001 atau A-001"
                               class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 font-mono text-xs font-bold outline-hidden">
                        <button type="submit"
                                :disabled="loading"
                                class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 disabled:opacity-60 text-white font-bold text-xs shadow-xs transition cursor-pointer shrink-0">
                            <span x-show="!loading">Cek</span>
                            <span x-show="loading" x-cloak>...</span>
                        </button>
                    </div>
                </form>

                <!-- Camera Barcode Scanner -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Kamera Pemindai 1D/QR</span>
                        <button type="button"
                                @click="cameraActive ? stopScanner() : startScanner()"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                :class="cameraActive ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-slate-900 text-white hover:bg-slate-800'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /></svg>
                            <span x-text="cameraActive ? 'Matikan Kamera' : 'Buka Kamera'"></span>
                        </button>
                    </div>

                    <div x-show="cameraActive" x-cloak class="rounded-2xl overflow-hidden border border-teal-200 bg-slate-900 p-2">
                        <div id="dokter-barcode-reader" class="w-full"></div>
                    </div>

                    <div x-show="!cameraActive" class="p-5 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1.5">
                        <div class="text-xs font-semibold text-slate-600">Mendukung Barcode Code-128 &amp; QR</div>
                        <p class="text-[11px] text-slate-400">
                            Arahkan kamera ke barcode di bagian bawah tiket mahasiswa atau gunakan alat scanner USB.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Pick Verified Ticket Participants -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Antrean Tiket Aktif</h3>
                    <span class="text-[10px] text-slate-400">Klik untuk muat</span>
                </div>
                <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
                    @forelse($todayPatients as $tp)
                        @php
                            $tUser = $tp->user;
                            $tProf = $tUser?->profile;
                        @endphp
                        <button type="button"
                                @click="lookupCode('{{ $tp->ticket_barcode_code }}')"
                                class="w-full text-left p-2.5 rounded-2xl border border-slate-100 hover:border-teal-300 hover:bg-teal-50/50 transition flex items-center justify-between gap-2 cursor-pointer">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-xs truncate">{{ $tProf?->full_name ?: $tUser?->name }}</div>
                                <div class="text-[10px] font-mono text-slate-400 truncate">{{ $tp->ticket_barcode_code }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg bg-teal-100 text-teal-800 font-mono font-bold text-[11px] shrink-0">
                                {{ $tp->queue_code ?: '#' . $tp->id }}
                            </span>
                        </button>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada antrean aktif.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Instant Student Profile + Health History Result (8 cols) -->
        <div class="xl:col-span-8 space-y-4">

            <!-- Toolbar when result active -->
            <div x-show="record" x-cloak class="flex items-center justify-between bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-xs print:hidden">
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-slate-800">Hasil Pemindaian Tiket Mahasiswa</span>
                </div>
                <button type="button" @click="resetScan()"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                    Scan Peserta Berikutnya
                </button>
            </div>

            <!-- Patient Full Record Card -->
            @include('dokter.partials.patient-record-panel')

            <!-- Empty / Standby State before scanning -->
            <div x-show="!record" x-cloak
                 class="bg-white rounded-3xl p-10 sm:p-14 border border-slate-200 shadow-xs text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-3xl shadow-2xs">
                    🪪
                </div>
                <div class="max-w-md mx-auto space-y-1.5">
                    <h3 class="text-lg font-extrabold text-slate-900">Menunggu Pemindaian Barcode Tiket</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Silakan pindai barcode pada bagian bawah tiket mahasiswa atau masukkan kode tiket (<span class="font-mono font-bold text-teal-700">SPK-{{ date('Y') }}-PPKMB-A-001</span>), nomor antrean, maupun NIM untuk menampilkan data diri dan riwayat kesehatan peserta secara otomatis.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

