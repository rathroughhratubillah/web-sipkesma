@extends('layouts.student')

@section('title', 'Konfirmasi & Pembayaran Skrining')

@section('content')
<div class="space-y-8"
     x-data="{
        paymentMethod: 'seabank',
        copiedText: null,
        previewQrisModal: false,
        filePreview: null,
        selectedMethodInput: 'SeaBank',
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2500);
        },
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => { this.filePreview = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.filePreview = null;
            }
        },
        scrollToUpload() {
            document.getElementById('upload-section')?.scrollIntoView({ behavior: 'smooth' });
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
     }">

    <div class="max-w-4xl mx-auto space-y-8 pb-16">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Konfirmasi & Pembayaran</h1>
                <p class="text-sm text-slate-500 mt-1">Selesaikan transfer pembayaran biaya skrining kesehatan PPKMB melalui SeaBank atau DANA.</p>
            </div>
            <button @click="scrollToUpload()" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold transition flex items-center gap-2">
                <span>Form Unggah Bukti</span>
                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
            </button>
        </div>

        <!-- Status Banner if Already Uploaded -->
        @if($payment)
            @if($payment->status === 'verified')
                <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-emerald-950">Pembayaran Telah Terverifikasi!</h2>
                            <p class="text-xs text-emerald-700 mt-0.5">Diverifikasi via <strong>{{ $payment->bank_name }}</strong> pada {{ $payment->verified_at?->format('d/m/Y H:i') }} WIB.</p>
                        </div>
                    </div>
                    <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-t border-emerald-200">
                        <span class="text-xs font-semibold text-emerald-800">
                            Nomor Antrean Anda: <strong class="text-lg font-mono font-black text-teal-800 tracking-wider bg-white px-3 py-1 rounded-lg border border-emerald-300 shadow-2xs">{{ $registration->queue_code ?? 'A-001' }}</strong>
                        </span>
                        <a href="{{ route('student.ticket') }}" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                            <span>Buka Antrean</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                    </div>
                </div>
            @elseif($payment->status === 'pending')
                <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-extrabold text-amber-950">Menunggu Verifikasi Panitia Medis</h2>
                            <p class="text-xs text-amber-800 mt-0.5">
                                Bukti transfer via <strong>{{ $payment->bank_name }}</strong> atas nama <strong>{{ $payment->sender_name }}</strong> sudah diterima sistem pada {{ $payment->updated_at->format('d/m/Y H:i') }} WIB dan sedang diproses admin.
                            </p>
                        </div>
                    </div>
                    @if($payment->proof_file_path)
                        <div class="pt-2 text-xs flex items-center gap-2 border-t border-amber-200">
                            <span class="text-amber-800">File bukti tersimpan:</span>
                            <a href="{{ asset($payment->proof_file_path) }}" target="_blank" class="text-amber-900 underline font-semibold flex items-center gap-1 hover:text-amber-700">
                                <span>Lihat file yang diunggah</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                            </a>
                        </div>
                    @endif
                </div>
            @elseif($payment->status === 'rejected')
                <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-900 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-rose-950">Pembayaran Belum Disetujui (Ditolak)</h2>
                            <p class="text-xs text-rose-800 mt-0.5"><span class="font-bold">Alasan Penolakan:</span> {{ $payment->rejection_reason }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-rose-700">Silakan periksa kembali kejelasan foto struk atau nominal pembayaran, lalu unggah ulang melalui formulir di bawah ini.</p>
                </div>
            @endif
        @endif

        <!-- 1. Card Ringkasan Booking / Registrasi -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Ringkasan Registrasi Skrining</h2>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                    PPKMB 2026
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs sm:text-sm">
                <div class="space-y-3">
                    <div>
                        <span class="text-slate-400 block text-xs">Layanan</span>
                        <span class="font-bold text-teal-700 text-sm sm:text-base">Pemeriksaan Skrining Medis PPKMB</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Tanggal Pelaksanaan</span>
                        <span class="font-extrabold text-slate-800 text-sm sm:text-base">{{ $sessionDate }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-slate-400 block text-xs">Fasilitas / Lokasi</span>
                        <span class="font-bold text-slate-700 text-sm sm:text-base">Klinik Kampus – Gedung A</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">Jam Sesi</span>
                        <span class="font-extrabold text-slate-800 text-sm sm:text-base">{{ $sessionTime }}</span>
                    </div>
                </div>
            </div>

            <!-- Biaya / Cara Pembayaran -->
            <div class="pt-4 border-t border-slate-100 space-y-1.5">
                <span class="text-slate-400 block text-xs font-medium">Total Biaya Pemeriksaan</span>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-teal-700 font-mono tracking-tight">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-xs">
                        Biaya Skrining Lengkap (3 Stasiun)
                    </span>
                </div>
                <p class="text-[11px] text-slate-500">
                    Mencakup 3 stasiun pemeriksaan: Tes Buta Warna, Skrining NAPZA 6 Parameter, dan Pemeriksaan Fisik & Dokter.
                </p>
            </div>
        </div>

        <!-- 2. Card Data Diri Anda -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Data Diri Anda</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nomor Antrean / Kode Registrasi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor Antrean / Kode Pendaftaran</label>
                    <div class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm flex items-center justify-between">
                        <span class="font-mono font-bold text-teal-700">
                            {{ $registration->queue_code ?: '#SIPKESMA-REG-' . str_pad($registration->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                        @if($registration->queue_code)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Terbit</span>
                        @else
                            <span class="text-[10px] font-medium text-slate-500">Aktif Setelah Verifikasi Admin</span>
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Nomor antrean fisik diterbitkan setelah admin memverifikasi bukti bayar.</span>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" readonly value="{{ $user->profile?->full_name ?? $user->name }}"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 font-semibold focus:outline-none">
                </div>

                <!-- NIM & Program Studi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">NIM / NISN & Program Studi</label>
                    <input type="text" readonly value="{{ ($user->profile?->nim ?? 'MHS') . ' — ' . ($user->profile?->major ?? 'Informatika') }}"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-700 focus:outline-none">
                </div>

                <!-- No HP / WhatsApp -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">No HP / WhatsApp</label>
                    <input type="text" readonly value="{{ $user->profile?->phone ?? '0882000260420' }}"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 font-mono focus:outline-none">
                    <span class="text-[10px] text-slate-400 mt-1 block">Nomor WhatsApp aktif untuk konfirmasi informasi skrining.</span>
                </div>
            </div>
        </div>

        <!-- 3. Card Metode Pembayaran (Only SeaBank & DANA) -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
            
            <div class="space-y-3">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Rekening Tujuan Transfer</h2>
                </div>

                <!-- Two Rounded Pills: SeaBank & DANA -->
                <div class="flex items-center gap-3">
                    <!-- SeaBank Pill Button -->
                    <button type="button"
                            @click="paymentMethod = 'seabank'; selectedMethodInput = 'SeaBank'"
                            class="px-6 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-2"
                            :class="paymentMethod === 'seabank' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'">
                        <span class="w-2 h-2 rounded-full bg-white" x-show="paymentMethod === 'seabank'"></span>
                        <span>Transfer SeaBank</span>
                    </button>

                    <!-- DANA Pill Button -->
                    <button type="button"
                            @click="paymentMethod = 'dana'; selectedMethodInput = 'DANA'"
                            class="px-6 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all flex items-center gap-2"
                            :class="paymentMethod === 'dana' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200'">
                        <span class="w-2 h-2 rounded-full bg-white" x-show="paymentMethod === 'dana'"></span>
                        <span>DANA / QRIS</span>
                    </button>
                </div>
            </div>

            <!-- ===================== VIEW METODE SEABANK ===================== -->
            <div x-show="paymentMethod === 'seabank'" x-transition:enter="transition ease-out duration-200" class="space-y-5">
                
                <!-- SeaBank Account Card -->
                <div class="p-5 sm:p-6 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-2xs space-y-2 relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-black uppercase tracking-wider text-amber-800 block mb-1">
                                NOMOR REKENING SEABANK
                            </span>
                            <div class="text-2xl sm:text-3xl font-black font-mono tracking-wider text-slate-900 select-all">
                                {{ $seabankAccount['number'] }}
                            </div>
                            <span class="text-xs text-slate-600 block mt-1 font-medium">
                                atas nama: <strong class="text-slate-800">{{ $seabankAccount['name'] }}</strong>
                            </span>
                        </div>

                        <button type="button"
                                @click="copyToClipboard('{{ $seabankAccount['number'] }}', 'seabank')"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.216.03-.422.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                            <span x-text="copiedText === 'seabank' ? '✓ Nomor Tersalin!' : 'Salin Nomor Rekening'"></span>
                        </button>
                    </div>
                </div>

                <!-- Transfer Instructions List -->
                <div class="space-y-2 text-xs sm:text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="font-bold text-slate-800 text-xs mb-2">Panduan Transfer SeaBank:</div>
                    <ol class="space-y-2 list-none">
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">1.</span>
                            <span>Buka aplikasi <strong>SeaBank</strong> di ponsel Anda atau mesin ATM / m-Banking bank lain.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">2.</span>
                            <span>Pilih menu <strong>Transfer &gt; Sesama SeaBank / Transfer ke Bank Lain</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">3.</span>
                            <span>Pilih bank tujuan <strong>SeaBank</strong> dan masukkan nomor rekening: <strong class="font-mono text-slate-900 select-all">{{ $seabankAccount['number'] }}</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">4.</span>
                            <span>Masukkan jumlah transfer yang <strong>sama persis</strong>: <strong class="text-teal-700 font-mono font-bold">Rp {{ number_format($amount, 0, ',', '.') }}</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">5.</span>
                            <span>Pastikan nama penerima adalah <strong>{{ $seabankAccount['name'] }}</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">6.</span>
                            <span>Lanjutkan transaksi dan masukkan PIN untuk menyelesaikan transfer.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">7.</span>
                            <span>Simpan bukti transfer berupa struk fisik atau tangkapan layar (screenshot).</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="font-bold text-amber-600 shrink-0">8.</span>
                            <span>Gulir ke formulir di bawah ini untuk mengunggah bukti pembayaran Anda.</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- ===================== VIEW METODE DANA ===================== -->
            <div x-show="paymentMethod === 'dana'" x-transition:enter="transition ease-out duration-200" class="space-y-5">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left: Number Box & Instructions -->
                    <div class="lg:col-span-7 space-y-4">
                        
                        <!-- DANA Account Card -->
                        <div class="p-5 sm:p-6 rounded-2xl bg-sky-50/70 border border-sky-200 shadow-2xs space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-sky-800 block mb-1">
                                        NOMOR DANA / E-WALLET
                                    </span>
                                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-wider text-slate-900 select-all">
                                        {{ $danaAccount['number'] }}
                                    </div>
                                    <span class="text-xs text-slate-600 block mt-1 font-medium">
                                        atas nama: <strong class="text-slate-800">{{ $danaAccount['name'] }}</strong>
                                    </span>
                                </div>

                                <button type="button"
                                        @click="copyToClipboard('{{ $danaAccount['number'] }}', 'dana')"
                                        class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.849A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.599m7.332 0c.055.194.084.4.084.615v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.216.03-.422.084-.615m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                                    <span x-text="copiedText === 'dana' ? '✓ Nomor Tersalin!' : 'Salin Nomor DANA'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- DANA Instructions List -->
                        <div class="space-y-2 text-xs sm:text-[13px] text-slate-600 leading-relaxed bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <div class="font-bold text-slate-800 text-xs mb-2">Panduan Pembayaran DANA / QRIS:</div>
                            <ol class="space-y-2 list-none">
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">1.</span>
                                    <span>Buka aplikasi <strong>DANA</strong> atau aplikasi perbankan apa saja yang mendukung QRIS.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">2.</span>
                                    <span>Pilih menu <strong>Kirim ke Nomor: <span class="font-mono text-slate-900">{{ $danaAccount['number'] }}</span></strong> atau tekan <strong>Scan QR</strong>.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">3.</span>
                                    <span>Arahkan kamera ke QR Code yang tampil di samping layar.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">4.</span>
                                    <span>Masukkan nominal transfer: <strong class="text-teal-700 font-mono font-bold">Rp {{ number_format($amount, 0, ',', '.') }}</strong>.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">5.</span>
                                    <span>Pastikan penerima adalah <strong>{{ $danaAccount['name'] }}</strong>.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">6.</span>
                                    <span>Masukkan PIN dan selesaikan transaksi.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">7.</span>
                                    <span>Simpan screenshot bukti transfer berhasil.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="font-bold text-sky-600 shrink-0">8.</span>
                                    <span>Unggah screenshot pada formulir di bawah ini.</span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <!-- Right: QR Code Visual Card -->
                    <div class="lg:col-span-5 p-5 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="px-2 py-0.5 rounded bg-sky-500 text-white font-bold text-xs">DANA &middot; QRIS</span>
                            <span class="text-[10px] font-mono text-slate-400">NMID: ID1020261928374</span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-slate-200 inline-block shadow-2xs">
                            @if($danaQrSvg)
                                <div class="w-48 h-48 sm:w-56 sm:h-56 flex items-center justify-center mx-auto">
                                    {!! $danaQrSvg !!}
                                </div>
                            @else
                                <div class="w-48 h-48 bg-slate-100 flex items-center justify-center text-slate-400 text-xs">
                                    QR Code DANA
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="text-xs font-bold text-slate-800">Scan QR Code DANA / QRIS</div>
                            <div class="text-[11px] text-slate-500">Nominal: <strong class="text-teal-700 font-mono font-bold">Rp {{ number_format($amount, 0, ',', '.') }}</strong></div>
                        </div>

                        <button type="button"
                                @click="previewQrisModal = true"
                                class="w-full py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" /></svg>
                            <span>Perbesar QR Code</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>

        <!-- 4. Formulir Pengunggahan Bukti Pembayaran -->
        @if($payment && $payment->status === 'verified')
            {{-- Sudah diverifikasi admin: tampilkan ringkasan bukti, sembunyikan form upload --}}
            <div id="upload-section" class="p-6 sm:p-8 rounded-3xl bg-white border border-emerald-200 shadow-sm space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center shrink-0 shadow-sm text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-emerald-900 uppercase tracking-wider">Bukti Pembayaran Diterima & Disetujui</h2>
                        <p class="text-xs text-slate-500">Tiket antrean Anda sudah aktif. Silakan unduh atau bawa saat datang ke klinik.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Metode Pembayaran</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->bank_name }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Nama Pengirim</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->sender_name }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Waktu Verifikasi</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->verified_at?->format('d/m/Y H:i') ?? $payment->updated_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                </div>
                @if($payment->proof_file_path)
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                        <span class="text-xs text-slate-600">Berkas bukti transfer:</span>
                        <a href="{{ asset($payment->proof_file_path) }}" target="_blank" class="text-teal-700 hover:underline font-semibold text-xs flex items-center gap-1">
                            <span>Buka file yang diunggah</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    </div>
                @endif
                <a href="{{ route('student.ticket') }}" class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                    <span>Lihat & Unduh Tiket Antrean Saya</span>
                </a>
            </div>
        @elseif($payment && $payment->status === 'pending')
            {{-- Menunggu verifikasi admin: tampilkan info pending, sembunyikan form upload --}}
            <div id="upload-section" class="p-6 sm:p-8 rounded-3xl bg-white border border-amber-200 shadow-sm space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-amber-900 uppercase tracking-wider">Bukti Diterima — Menunggu Verifikasi Admin</h2>
                        <p class="text-xs text-slate-500">Tiket antrean akan otomatis aktif setelah admin menyetujui pembayaran Anda.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Metode Pembayaran</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->bank_name }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Nama Pengirim</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->sender_name }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block mb-1">Waktu Unggah</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $payment->updated_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                </div>
                @if($payment->proof_file_path)
                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                        <span class="text-xs text-slate-600">Berkas bukti tersimpan:</span>
                        <a href="{{ asset($payment->proof_file_path) }}" target="_blank" class="text-amber-800 hover:underline font-semibold text-xs flex items-center gap-1">
                            <span>Lihat file yang diunggah</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    </div>
                @endif
                <p class="text-xs text-amber-800 text-center bg-amber-50/60 p-3 rounded-xl border border-amber-100">
                    💡 Mohon tunggu proses verifikasi admin. Anda dapat mengecek status secara berkala di portal ini.
                </p>
            </div>
        @else
        {{-- Belum bayar atau ditolak: tampilkan form upload --}}
        <div id="upload-section" class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-100 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Unggah Bukti Transfer Pembayaran</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah foto struk ATM, bukti transfer SeaBank, atau tangkapan layar transaksi DANA Anda.</p>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                    Total: Rp {{ number_format($amount, 0, ',', '.') }}
                </span>
            </div>

            <form method="POST" action="{{ route('student.payment.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Pilihan Metode / Bank yang Digunakan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Metode Pembayaran Yang Anda Gunakan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition"
                               :class="selectedMethodInput === 'SeaBank' ? 'bg-amber-50/80 border-amber-400 text-slate-900 shadow-2xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300'">
                            <input type="radio" name="bank_name" value="SeaBank" x-model="selectedMethodInput" class="text-amber-500 focus:ring-0">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Transfer SeaBank</span>
                                <span class="block text-[11px] text-slate-500 font-mono">No Rek: 901028036540</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition"
                               :class="selectedMethodInput === 'DANA' ? 'bg-sky-50/80 border-sky-400 text-slate-900 shadow-2xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300'">
                            <input type="radio" name="bank_name" value="DANA" x-model="selectedMethodInput" class="text-sky-500 focus:ring-0">
                            <div>
                                <span class="block text-xs font-bold text-slate-900">E-Wallet DANA / QRIS</span>
                                <span class="block text-[11px] text-slate-500 font-mono">Nomor: 0882000260420</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Input Pengirim & WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Pengirim / Pemilik Rekening <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_name" required
                               value="{{ old('sender_name', $payment->sender_name ?? ($user->profile?->full_name ?? $user->name)) }}"
                               placeholder="Nama di akun SeaBank atau DANA Anda"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:border-teal-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            No. WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="sender_phone" required
                               value="{{ old('sender_phone', $user->profile?->phone ?? '0882000260420') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-sm focus:border-teal-500 focus:bg-white focus:outline-none font-mono transition">
                    </div>
                </div>

                <!-- File Bukti Transfer -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        File Foto / Screenshot Bukti Transfer (JPG, PNG, PDF maks. 5MB) <span class="text-rose-500">*</span>
                    </label>
                    <div class="p-5 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 hover:border-teal-500 transition space-y-3">
                        <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required
                               @change="handleFileSelect($event)"
                               class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer">

                        <!-- Instant File Preview if Image -->
                        <template x-if="filePreview">
                            <div class="pt-2 flex items-center gap-3">
                                <img :src="filePreview" class="w-20 h-20 object-cover rounded-xl border border-slate-200 shadow-sm">
                                <span class="text-xs text-emerald-700 font-semibold">✓ Foto bukti siap dikirimkan</span>
                            </div>
                        </template>

                        @error('proof_file')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tombol Submit Kirim Bukti -->
                <div class="pt-3">
                    <button type="submit" 
                            class="w-full py-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        <span>{{ $payment && $payment->status === 'rejected' ? 'Unggah Ulang Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-400 mt-2">
                        ✓ Setelah bukti diunggah, admin akan memverifikasi dan menerbitkan tiket antrean Anda.
                    </p>
                </div>
            </form>

        </div>
        @endif

    </div>

    <!-- Floating Scroll Controls (Matching Circular Button in Screenshots 1 & 2) -->
    <div class="fixed bottom-6 right-6 z-40 flex flex-col gap-2">
        <button type="button"
                @click="scrollToUpload()"
                class="w-12 h-12 rounded-full bg-amber-500 hover:bg-amber-400 text-slate-950 flex items-center justify-center shadow-2xl shadow-amber-500/50 transition transform hover:scale-105"
                title="Gulir ke Formulir Bukti Bayar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
        </button>
        <button type="button"
                @click="scrollToTop()"
                class="w-12 h-12 rounded-full bg-slate-100/90 hover:bg-slate-200 text-slate-600 border border-slate-200 flex items-center justify-center shadow-xl transition"
                title="Kembali ke Atas">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
        </button>
    </div>

    <!-- Modal Perbesar QR Code DANA -->
    <div x-show="previewQrisModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 space-y-4 shadow-2xl border border-slate-200 text-center text-slate-900" 
             @click.outside="previewQrisModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="px-2.5 py-1 rounded-lg bg-sky-600 text-slate-900 font-black text-xs">DANA &middot; QRIS</span>
                <button @click="previewQrisModal = false" class="text-slate-500 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div>
                <div class="text-base font-black text-slate-900">KLINIK SIPKESMA PPKMB</div>
                <div class="text-xs text-slate-500 font-mono">DANA: {{ $danaAccount['number'] }} &middot; Rp {{ number_format($amount, 0, ',', '.') }}</div>
            </div>

            <div class="p-3 bg-white rounded-2xl border-2 border-slate-200 inline-block shadow-sm">
                @if($danaQrSvg)
                    <div class="w-64 h-64 flex items-center justify-center mx-auto">
                        {!! $danaQrSvg !!}
                    </div>
                @endif
            </div>

            <p class="text-xs text-slate-500">Arahkan kamera aplikasi DANA atau Mobile Banking Anda ke layar.</p>

            <div class="pt-2">
                <button @click="previewQrisModal = false" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                    Tutup Tampilan QR
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

