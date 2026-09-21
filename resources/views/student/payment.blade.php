@extends('layouts.student')

@section('title', 'Pembayaran Skrining')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pembayaran Skrining Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-1">Lakukan transfer biaya skrining PPKMB dan unggah bukti transfer di bawah ini.</p>
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
                        <p class="text-xs text-amber-700 mt-0.5">Bukti pembayaran Anda sudah diterima pada {{ $payment->updated_at->format('d/m/Y H:i') }} WIB dan sedang dalam antrean pemeriksaan panitia.</p>
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

    <!-- Tagihan & Rekening Pembayaran -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <!-- Nominal Tagihan -->
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

        <!-- Rekening Bank Resmi Kampus -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pilihan Rekening Pembayaran Resmi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-data="{ copied: null }">
                @foreach($bankAccounts as $bank)
                    <div class="p-4 rounded-2xl border border-slate-200 hover:border-teal-400 bg-slate-50/50 transition space-y-2">
                        <div class="text-xs font-bold text-slate-800">{{ $bank['bank'] }}</div>
                        <div class="text-sm font-extrabold text-teal-800 font-mono tracking-wide select-all">{{ $bank['number'] }}</div>
                        <div class="text-[11px] text-slate-500">a.n. {{ $bank['name'] }}</div>
                        
                        <button type="button" 
                                @click="navigator.clipboard.writeText('{{ str_replace('-', '', $bank['number']) }}'); copied = '{{ $bank['bank'] }}'; setTimeout(() => copied = null, 2000)"
                                class="w-full py-1.5 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[11px] font-semibold text-slate-700 transition">
                            <span x-text="copied === '{{ $bank['bank'] }}' ? '✓ Tersalin' : 'Salin Nomor Rekening'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Upload Form (Disabled only if verified) -->
        @if(!$payment || $payment->status !== 'verified')
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Unggah Bukti Transfer</h3>
                
                <form method="POST" action="{{ route('student.payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bank Tujuan Transfer <span class="text-rose-500">*</span></label>
                            <select name="bank_name" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden bg-white">
                                <option value="Bank Syariah Indonesia (BSI)">Bank Syariah Indonesia (BSI)</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="Bank BNI">Bank BNI</option>
                                <option value="Bank Lainnya / QRIS">Bank Lainnya / QRIS</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pengirim / Pemilik Rekening <span class="text-rose-500">*</span></label>
                            <input type="text" name="sender_name" value="{{ old('sender_name', $user->name) }}" required
                                   placeholder="Contoh: Ratu Ayu Maharani"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">File Foto / Struk Transfer (JPG, PNG, PDF maks. 3MB) <span class="text-rose-500">*</span></label>
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
</div>
@endsection
