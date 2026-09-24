@extends('layouts.student')

@section('title', 'Pembayaran Skrining')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{
    paymentMethod: 'qris',
    copied: null,
    previewQrisModal: false,
    selectedBank: 'QRIS (Semua E-Wallet & M-Banking)'
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pembayaran Skrining Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-1">Lakukan pembayaran biaya skrining PPKMB melalui QRIS atau Transfer Bank.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 6 & 7 dari 11
        </span>
    </div>

    <!-- Status Banner if Already Uploaded -->
    @if($payment)
        @if($payment->status === 'verified')
            <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-emerald-900">Pembayaran Terverifikasi!</h2>
                        <p class="text-xs text-emerald-700 mt-0.5">Diverifikasi oleh {{ $payment->verifier?->name ?? 'Panitia Medis' }} pada {{ $payment->verified_at?->format('d/m/Y H:i') }} WIB.</p>
                    </div>
                </div>
                <div class="pt-2 flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-800">Nomor Antrean Anda: <strong class="text-base font-mono font-bold">{{ $registration->queue_code }}</strong></span>
                    <a href="{{ route('student.ticket') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                        Buka Tiket Antrean & QR &rarr;
                    </a>
                </div>
            </div>
        @elseif($payment->status === 'pending')
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-amber-900">Menunggu Verifikasi Petugas Medis</h2>
                        <p class="text-xs text-amber-700 mt-0.5">Bukti pembayaran Anda via <strong>{{ $payment->bank_name }}</strong> sudah diterima pada {{ $payment->updated_at->format('d/m/Y H:i') }} WIB dan sedang dalam antrean pemeriksaan panitia.</p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'rejected')
            <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-900 space-y-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-rose-900">Pembayaran Ditolak</h2>
                        <p class="text-xs text-rose-700 mt-0.5"><span class="font-bold">Alasan Penolakan:</span> {{ $payment->rejection_reason }}</p>
                    </div>
                </div>
                <p class="text-xs text-rose-800">Silakan periksa kembali nominal atau kejelasan foto bukti transfer, lalu unggah ulang pada formulir di bawah ini.</p>
            </div>
        @endif
    @endif

    <!-- Tagihan Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <!-- Nominal Tagihan Banner -->
        <div class="flex items-center justify-between p-5 rounded-2xl bg-teal-50 border border-teal-100">
            <div>
                <span class="text-xs font-semibold text-teal-700 uppercase tracking-wider">Total Biaya Skrining PPKMB</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-teal-900 mt-0.5">Rp 150.000</div>
                <span class="text-[11px] text-teal-600">Termasuk Tes Urin, Skrining 6 Parameter NAPZA & Pemeriksaan Fisik</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white text-teal-600 flex items-center justify-center shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
        </div>

        <!-- Pilihan Metode Pembayaran Switcher -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pilih Metode Pembayaran</h3>
                <span class="text-[11px] text-slate-400">Pilih salah satu metode pembayaran di bawah</span>
            </div>

            <!-- Tab Buttons -->
            <div class="grid grid-cols-2 gap-3 p-1.5 rounded-2xl bg-slate-100/80 border border-slate-200">
                <button type="button" 
                        @click="paymentMethod = 'qris'; selectedBank = 'QRIS (Semua E-Wallet & M-Banking)'"
                        class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all"
                        :class="paymentMethod === 'qris' ? 'bg-white text-teal-900 shadow-sm border border-slate-200/80' : 'text-slate-500 hover:text-slate-900'">
                    <span class="px-1.5 py-0.5 rounded-md bg-rose-600 text-white font-extrabold text-[10px]">QRIS</span>
                    <span>QRIS (E-Wallet & m-Banking)</span>
                </button>
                <button type="button" 
                        @click="paymentMethod = 'bank'; selectedBank = 'Bank Syariah Indonesia (BSI)'"
                        class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all"
                        :class="paymentMethod === 'bank' ? 'bg-white text-teal-900 shadow-sm border border-slate-200/80' : 'text-slate-500 hover:text-slate-900'">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5m-15 0V21" /></svg>
                    <span>Transfer Bank Manual</span>
                </button>
            </div>

            <!-- 1. TAMPILAN KODE QRIS -->
            <div x-show="paymentMethod === 'qris'" x-transition class="space-y-5">
                <div class="p-6 rounded-3xl bg-gradient-to-b from-slate-50 to-white border-2 border-slate-200 shadow-sm flex flex-col items-center text-center space-y-4">
                    
                    <!-- Header Kartu QRIS -->
                    <div class="w-full flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-rose-600 text-white font-black text-xs tracking-wider">QRIS</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest hidden sm:inline">Pembayaran Digital Nasional</span>
                        </div>
                        <span class="text-[10px] font-mono font-semibold text-slate-400">NMID: ID1020261928374</span>
                    </div>

                    <!-- Merchant Info -->
                    <div>
                        <div class="text-xs text-slate-400 uppercase font-semibold">Merchant Resmi</div>
                        <div class="text-base sm:text-lg font-black text-slate-900">KLINIK SIPKESMA PPKMB UINSSC</div>
                        <div class="text-[11px] text-teal-600 font-semibold mt-0.5">Kode Registrasi: SIPKESMA-{{ $registration->id }}-{{ $user->profile?->nim ?? 'MHS' }}</div>
                    </div>

                    <!-- Visual QR Code Container -->
                    <div class="p-4 rounded-2xl bg-white border-2 border-slate-300 shadow-md inline-block relative group">
                        @if($qrisSvg)
                            <div class="w-56 h-56 flex items-center justify-center mx-auto">
                                {!! $qrisSvg !!}
                            </div>
                        @else
                            <div class="w-56 h-56 flex flex-col items-center justify-center bg-slate-100 rounded-xl text-slate-400 text-xs p-4">
                                <svg class="w-10 h-10 mb-2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" /></svg>
                                <span>QRIS Code Sipkesma</span>
                            </div>
                        @endif

                        <div class="mt-2 text-center">
                            <span class="text-[11px] font-bold text-slate-700">Nominal: <strong class="text-teal-700 font-mono text-sm">Rp 150.000</strong></span>
                        </div>
                    </div>

                    <!-- Supported Apps Badges -->
                    <div class="w-full pt-2">
                        <div class="text-[11px] font-semibold text-slate-500 mb-2">Dapat dipindai dari semua aplikasi perbankan & e-wallet:</div>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            @foreach(['BCA Mobile', 'Livin Mandiri', 'BRImo', 'BNI Mobile', 'BSI Mobile', 'GoPay', 'OVO', 'DANA', 'ShopeePay', 'LinkAja'] as $app)
                                <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-700 shadow-2xs">
                                    {{ $app }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                        <button type="button" 
                                @click="previewQrisModal = true"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" /></svg>
                            <span>Perbesar Layar Penuh</span>
                        </button>
                    </div>
                </div>

                <!-- Panduan Cara Bayar QRIS -->
                <div class="p-5 rounded-2xl bg-teal-50/70 border border-teal-100 text-xs text-slate-700 space-y-2">
                    <div class="font-bold text-teal-900 flex items-center gap-1.5 text-sm">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                        <span>Cara Membayar dengan QRIS:</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1.5 text-slate-600">
                        <li>Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI, BSI) atau e-Wallet (GoPay, OVO, DANA, ShopeePay).</li>
                        <li>Pilih menu <strong>Scan QR / Bayar</strong> dan arahkan kamera ke kode QR di atas (atau unggah screenshot kode QR).</li>
                        <li>Pastikan nama merchant penerima: <strong>KLINIK SIPKESMA PPKMB</strong> dan nominal <strong>Rp 150.000</strong>.</li>
                        <li>Selesaikan pembayaran dan simpan / screenshot bukti transfer yang berhasil.</li>
                        <li>Unggah foto/tangkapan layar bukti transfer pada formulir verifikasi di bawah.</li>
                    </ol>
                </div>
            </div>

            <!-- 2. TAMPILAN TRANSFER BANK MANUAL -->
            <div x-show="paymentMethod === 'bank'" x-transition class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($bankAccounts as $bank)
                        <div class="p-4 rounded-2xl border border-slate-200 hover:border-teal-400 bg-slate-50/50 transition space-y-2">
                            <div class="text-xs font-bold text-slate-800">{{ $bank['bank'] }}</div>
                            <div class="text-sm font-extrabold text-teal-800 font-mono tracking-wide select-all">{{ $bank['number'] }}</div>
                            <div class="text-[11px] text-slate-500">a.n. {{ $bank['name'] }}</div>
                            
                            <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ str_replace('-', '', $bank['number']) }}'); copied = '{{ $bank['bank'] }}'; selectedBank = '{{ $bank['bank'] }}'; setTimeout(() => copied = null, 2000)"
                                    class="w-full py-1.5 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[11px] font-semibold text-slate-700 transition">
                                <span x-text="copied === '{{ $bank['bank'] }}' ? '✓ Tersalin & Dipilih' : 'Salin Nomor Rekening'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Formulir Unggah Bukti Bayar (Disabled only if verified) -->
        @if(!$payment || $payment->status !== 'verified')
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Unggah Bukti Pembayaran</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah screenshot struk / notifikasi bukti pembayaran Anda</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('student.payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Metode / Bank Pengirim <span class="text-rose-500">*</span></label>
                            <select name="bank_name" x-model="selectedBank" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden bg-white">
                                <option value="QRIS (Semua E-Wallet & M-Banking)">QRIS (Semua E-Wallet & M-Banking)</option>
                                <option value="Bank Syariah Indonesia (BSI)">Bank Syariah Indonesia (BSI)</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="Bank BNI">Bank BNI</option>
                                <option value="Bank BCA / Bank Lainnya">Bank BCA / Bank Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pengirim / Pemilik Akun <span class="text-rose-500">*</span></label>
                            <input type="text" name="sender_name" value="{{ old('sender_name', $user->profile?->full_name ?? $user->name) }}" required
                                   placeholder="Contoh: Ratu Ayu Maharani"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">File Bukti Transfer / Struk QRIS (JPG, PNG, PDF maks. 3MB) <span class="text-rose-500">*</span></label>
                        <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required
                               class="w-full px-4 py-2 rounded-xl border border-dashed border-slate-300 focus:border-teal-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        @error('proof_file') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/25 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            <span>{{ $payment && $payment->status === 'rejected' ? 'Unggah Ulang Bukti Bayar' : 'Kirim Bukti Pembayaran' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

    <!-- Modal Perbesar Kode QRIS -->
    <div x-show="previewQrisModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100 text-center" @click.outside="previewQrisModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="px-2.5 py-1 rounded-lg bg-rose-600 text-white font-black text-xs">QRIS NASIONAL</span>
                <button @click="previewQrisModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div>
                <div class="text-base font-black text-slate-900">KLINIK SIPKESMA PPKMB UINSSC</div>
                <div class="text-xs text-slate-500">NMID: ID1020261928374 &middot; Total: <strong>Rp 150.000</strong></div>
            </div>

            <div class="p-4 bg-white rounded-2xl border-2 border-slate-200 inline-block">
                @if($qrisSvg)
                    <div class="w-64 h-64 flex items-center justify-center mx-auto">
                        {!! $qrisSvg !!}
                    </div>
                @endif
            </div>

            <p class="text-xs text-slate-500">Arahkan kamera aplikasi bank / e-wallet Anda ke layar untuk membayar.</p>

            <div class="flex justify-center pt-2">
                <button @click="previewQrisModal = false" class="px-6 py-2.5 rounded-xl bg-teal-600 text-white text-xs font-bold hover:bg-teal-700 transition">
                    Tutup Tampilan
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
