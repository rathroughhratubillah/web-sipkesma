@extends('layouts.admin')

@section('title', 'Hasil Upload Bukti Pembayaran')
@section('header_title', 'Hasil Upload Bukti Pembayaran')
@section('header_subtitle', 'Arsip dan rekap seluruh bukti transfer pembayaran yang diunggah oleh mahasiswa baru. Tiket antrean langsung diterbitkan otomatis oleh sistem.')

@section('content')
<div class="space-y-6" x-data="{
    previewModal: false,
    previewUrl: '',
    previewTitle: '',
    previewSender: '',
    previewBank: '',
    previewQueue: '',
    isPdf: false,
    openPreview(url, title, sender, bank, queue) {
        this.previewUrl = url;
        this.previewTitle = title;
        this.previewSender = sender;
        this.previewBank = bank;
        this.previewQueue = queue;
        this.isPdf = url.toLowerCase().endsWith('.pdf');
        this.previewModal = true;
    }
}">

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Bukti Masuk</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['total'] ?? $payments->total() }}</span>
                <span class="text-[11px] text-teal-600 font-semibold mt-0.5 block">&#10003; Tiket Langsung Terbit</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Via SeaBank</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['seabank'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 font-mono mt-0.5 block">901028036540</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-sm">
                SEA
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Via DANA / QRIS</span>
                <span class="text-2xl font-black text-sky-600 mt-1 block">{{ $stats['dana'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 font-mono mt-0.5 block">0882000260420</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-black text-sm">
                DANA
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Dana Tercatat</span>
                <span class="text-xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($stats['total_amount'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Biaya @ Rp 150.000</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Mahasiswa / No. Antrean</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik Nama, NIM, No. Antrean (A-001), atau Rekening..."
                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="min-w-[170px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Metode Transfer</label>
            <select name="method" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="all">Semua Metode</option>
                <option value="SeaBank" {{ ($method ?? '') === 'SeaBank' ? 'selected' : '' }}>SeaBank (901028036540)</option>
                <option value="DANA" {{ ($method ?? '') === 'DANA' ? 'selected' : '' }}>DANA (0882000260420)</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition flex items-center gap-1.5 shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <span>Cari Data</span>
        </button>
        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
            Reset
        </a>
    </form>

    <!-- Table of Uploaded Proofs -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Rekap Bukti Pembayaran &amp; Tiket</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar file bukti pembayaran yang diunggah mahasiswa beserta nomor antrean yang otomatis diterbitkan.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Total: <strong class="text-slate-800">{{ $payments->total() }}</strong> berkas
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Mahasiswa</th>
                        <th class="px-6 py-3.5">Nomor Antrean</th>
                        <th class="px-6 py-3.5">Bukti Transfer</th>
                        <th class="px-6 py-3.5">Metode &amp; Pengirim</th>
                        <th class="px-6 py-3.5">Nominal</th>
                        <th class="px-6 py-3.5">Waktu Upload</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                    @php
                        $user = $payment->registration?->user;
                        $profile = $user?->profile;
                        $proofPath = $payment->proof_file_path;
                        $proofUrl = $proofPath ? asset($proofPath) : null;
                        $isPdfFile = $proofPath ? str_ends_with(strtolower($proofPath), '.pdf') : false;
                        $studentName = $profile?->full_name ?: $user?->name ?: 'Mahasiswa';
                        $queueCode = $payment->registration?->queue_code ?: 'A-001';
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        
                        <!-- Mahasiswa -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 text-sm">
                                {{ $studentName }}
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                NIM: {{ $profile?->nim ?: 'Belum diisi' }}
                            </div>
                            <div class="text-[11px] text-slate-400">
                                {{ $profile?->faculty ?: $user?->email }}
                            </div>
                        </td>

                        <!-- Nomor Antrean Otomatis -->
                        <td class="px-6 py-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 font-mono font-black text-sm shadow-xs">
                                {{ $queueCode }}
                            </div>
                            <span class="block text-[10px] text-emerald-600 font-bold mt-1">&#10003; Tiket Aktif Otomatis</span>
                        </td>

                        <!-- Bukti Transfer (Thumbnail & View) -->
                        <td class="px-6 py-4">
                            @if($proofUrl)
                                <div class="flex items-center gap-3">
                                    @if($isPdfFile)
                                        <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold text-xs shrink-0 cursor-pointer shadow-xs hover:scale-105 transition"
                                             @click="openPreview('{{ $proofUrl }}', 'Bukti Transfer: {{ addslashes($studentName) }}', '{{ addslashes($payment->sender_name) }}', '{{ addslashes($payment->bank_name) }}', '{{ $queueCode }}')">
                                            PDF
                                        </div>
                                    @else
                                        <img src="{{ $proofUrl }}" 
                                             alt="Bukti Transfer" 
                                             class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-xs cursor-pointer hover:scale-105 transition shrink-0"
                                             @click="openPreview('{{ $proofUrl }}', 'Bukti Transfer: {{ addslashes($studentName) }}', '{{ addslashes($payment->sender_name) }}', '{{ addslashes($payment->bank_name) }}', '{{ $queueCode }}')">
                                    @endif

                                    <div class="space-y-1">
                                        <button type="button" 
                                                @click="openPreview('{{ $proofUrl }}', 'Bukti Transfer: {{ addslashes($studentName) }}', '{{ addslashes($payment->sender_name) }}', '{{ addslashes($payment->bank_name) }}', '{{ $queueCode }}')"
                                                class="text-teal-700 hover:text-teal-900 font-bold text-xs hover:underline flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span>Lihat Bukti</span>
                                        </button>
                                        <a href="{{ $proofUrl }}" target="_blank" download class="text-slate-400 hover:text-slate-600 text-[11px] block">
                                            Unduh Berkas &darr;
                                        </a>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 italic">Belum ada file</span>
                            @endif
                        </td>

                        <!-- Metode & Pengirim -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 mb-1">
                                @if(str_contains(strtolower($payment->bank_name ?? ''), 'seabank'))
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-[10px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        SeaBank
                                    </span>
                                @elseif(str_contains(strtolower($payment->bank_name ?? ''), 'dana'))
                                    <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-900 border border-sky-300 font-extrabold text-[10px] inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        DANA
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        {{ $payment->bank_name ?: 'Transfer Bank' }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-600 font-medium">
                                a.n. {{ $payment->sender_name ?: '-' }}
                            </div>
                        </td>

                        <!-- Nominal -->
                        <td class="px-6 py-4 font-mono font-bold text-slate-900">
                            Rp {{ number_format($payment->amount ?? 150000, 0, ',', '.') }}
                        </td>

                        <!-- Waktu Upload -->
                        <td class="px-6 py-4 text-slate-500 text-[11px]">
                            <div>{{ $payment->created_at->format('d/m/Y') }}</div>
                            <div class="text-slate-400 font-mono">{{ $payment->created_at->format('H:i') }} WIB</div>
                        </td>

                        <!-- Aksi Hapus (Jika Fake/Spam) -->
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip bukti pembayaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition font-bold text-[11px] inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            Belum ada bukti pembayaran yang diunggah oleh mahasiswa.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Pratinjau Bukti Transfer Interaktif -->
    <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.outside="previewModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900" x-text="previewTitle"></h3>
                    <p class="text-xs text-slate-400" x-text="'Metode: ' + previewBank + ' | a.n. ' + previewSender + ' | Antrean: ' + previewQueue"></p>
                </div>
                <button @click="previewModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto flex items-center justify-center bg-slate-50 rounded-2xl p-4 border border-slate-200">
                <template x-if="!isPdf">
                    <img :src="previewUrl" alt="Bukti Transfer" class="max-w-full h-auto max-h-[60vh] rounded-xl object-contain shadow-sm">
                </template>
                <template x-if="isPdf">
                    <div class="text-center py-12 space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto text-xl font-black">
                            PDF
                        </div>
                        <div class="text-sm font-bold text-slate-800">Dokumen Bukti Transfer PDF</div>
                        <p class="text-xs text-slate-400">Berkas ini berformat dokumen PDF. Buka berkas di tab baru untuk membaca dokumen.</p>
                        <a :href="previewUrl" target="_blank" class="px-5 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-xs inline-block shadow-md hover:bg-teal-700 transition">
                            Buka Dokumen PDF di Tab Baru &rarr;
                        </a>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a :href="previewUrl" target="_blank" download class="text-xs text-teal-700 font-bold hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Unduh Gambar Asli</span>
                </a>
                <button @click="previewModal = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
