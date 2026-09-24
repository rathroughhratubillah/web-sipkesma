@extends('layouts.staff')

@section('title', 'Verifikasi Pembayaran')
@section('header_title', 'Verifikasi Pembayaran Skrining')

@section('content')
<div class="space-y-6" x-data="{
    previewModal: false,
    previewUrl: '',
    previewTitle: '',
    rejectModal: false,
    rejectActionUrl: '',
    rejectStudentName: ''
}">

    <!-- Top Tabs & Stats -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 bg-white p-1.5 rounded-2xl border border-slate-200 text-xs font-semibold">
            <a href="{{ route('staff.payments.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-xl transition {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Menunggu ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('staff.payments.index', ['status' => 'verified']) }}" 
               class="px-4 py-2 rounded-xl transition {{ $status === 'verified' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Disetujui ({{ $counts['verified'] }})
            </a>
            <a href="{{ route('staff.payments.index', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-xl transition {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Ditolak ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('staff.payments.index', ['status' => 'all']) }}" 
               class="px-4 py-2 rounded-xl transition {{ $status === 'all' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Semua Riwayat
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Sesi Pilihan</th>
                        <th class="p-4">Bank & Pengirim</th>
                        <th class="p-4">Nominal</th>
                        <th class="p-4">Bukti Transfer</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($payments as $p)
                        @php
                            $user = $p->registration?->user;
                            $profile = $user?->profile;
                            $session = $p->registration?->testSession;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Mahasiswa -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $profile?->full_name ?: $user?->name }}</div>
                                <div class="text-slate-500 font-mono">{{ $profile?->nim ?: 'NISN belum ada' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $profile?->faculty ?: $user?->email }}</div>
                            </td>

                            <!-- Sesi -->
                            <td class="p-4 text-slate-600">
                                @if($session)
                                    <div class="font-bold text-slate-800">{{ $session->session_name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($session->session_date)->locale('id')->isoFormat('D MMM Y') }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Bank -->
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $p->bank_name ?: 'Bank Transfer' }}</div>
                                <div class="text-slate-500 text-[11px]">a.n. {{ $p->sender_name ?: '-' }}</div>
                            </td>

                            <!-- Nominal -->
                            <td class="p-4 font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($p->amount, 0, ',', '.') }}
                            </td>

                            <!-- Bukti -->
                            <td class="p-4">
                                @php
                                    $proofUrl = asset($p->proof_file_path);
                                @endphp
                                <button type="button" 
                                        @click="previewModal = true; previewUrl = '{{ $proofUrl }}'; previewTitle = 'Bukti Bayar: {{ addslashes($profile?->full_name ?: $user?->name) }}'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold text-[11px] transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span>Pratinjau</span>
                                </button>
                            </td>

                            <!-- Status -->
                            <td class="p-4">
                                @if($p->status === 'verified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Verified ({{ $p->registration->queue_code }})
                                    </span>
                                @elseif($p->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="{{ $p->rejection_reason }}">
                                        ✕ Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right">
                                @if($p->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Approve Form with DB Transaction -->
                                        <form method="POST" action="{{ route('staff.payments.approve', $p) }}" onsubmit="return confirm('Setujui pembayaran ini dan terbitkan nomor antrean secara otomatis?')">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                                                <span>Setujui</span>
                                            </button>
                                        </form>

                                        <!-- Reject Trigger -->
                                        <button type="button" 
                                                @click="rejectModal = true; rejectActionUrl = '{{ route('staff.payments.reject', $p) }}'; rejectStudentName = '{{ addslashes($profile?->full_name ?: $user?->name) }}'"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">{{ $p->verified_at?->format('d/m/Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Tidak ada data pembayaran pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Pratinjau Bukti Transfer -->
    <div x-show="previewModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.outside="previewModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900" x-text="previewTitle"></h3>
                <button @click="previewModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="max-h-[70vh] overflow-y-auto flex items-center justify-center bg-slate-50 rounded-2xl p-4">
                <img :src="previewUrl" alt="Bukti Transfer" class="max-w-full h-auto rounded-xl object-contain shadow-sm">
            </div>

            <div class="flex justify-end">
                <button @click="previewModal = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Form Tolak Pembayaran -->
    <div x-show="rejectModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.outside="rejectModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-rose-700">Tolak Pembayaran Mahasiswa</h3>
                <button @click="rejectModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form :action="rejectActionUrl" method="POST" class="space-y-4">
                @csrf
                <p class="text-xs text-slate-600">
                    Anda akan menolak pembayaran untuk <strong class="text-slate-900" x-text="rejectStudentName"></strong>. Mahasiswa akan diminta untuk mengunggah ulang bukti bayar yang benar.
                </p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required
                              placeholder="Contoh: Bukti transfer buram/tidak terbaca, nominal kurang dari Rp 150.000, dsb."
                              class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-rose-500 text-xs outline-hidden"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
