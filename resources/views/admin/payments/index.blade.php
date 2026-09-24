@extends('layouts.admin')

@section('title', 'Data Pembayaran')
@section('header_title', 'Kelola Data Pembayaran')
@section('header_subtitle', 'Semua data pembayaran yang tersimpan di database')

@section('content')
<div class="space-y-4" x-data="{
    previewModal: false,
    previewUrl: '',
    previewTitle: '',
    statusModal: false,
    statusActionUrl: '',
    currentStatus: '',
    studentName: '',
    rejectionReason: ''
}">

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Mahasiswa</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NISN..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Status Pembayaran</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Verified (Disetujui)</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">🔍 Cari</button>
        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Data Pembayaran Skrining</h2>
                <p class="text-xs text-slate-400">Total: {{ $payments->total() }} data transaksi</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">Bank & Pengirim</th>
                        <th class="px-5 py-3">Jumlah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Bukti Bayar</th>
                        <th class="px-5 py-3">Diverifikasi Oleh</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                    @php
                        $user = $payment->registration?->user;
                        $profile = $user?->profile;
                        $proofUrl = asset($payment->proof_file_path);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">
                                {{ $profile?->full_name ?? $user?->name ?? '—' }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">
                                {{ $profile?->nim ?? '—' }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-700">{{ $payment->bank_name ?: 'Bank Transfer' }}</div>
                            <div class="text-[11px] text-slate-400">a.n. {{ $payment->sender_name ?: '—' }}</div>
                        </td>
                        <td class="px-5 py-3 font-semibold text-slate-800 font-mono">
                            Rp {{ number_format($payment->amount ?? 150000, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $payment->status === 'verified' ? 'bg-emerald-100 text-emerald-700' :
                                   ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}"
                                  title="{{ $payment->rejection_reason }}">
                                {{ $payment->status === 'verified' ? '✓ Verified (' . ($payment->registration?->queue_code ?? 'TKT') . ')' : ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($payment->proof_file_path)
                                <button type="button" 
                                        @click="previewModal = true; previewUrl = '{{ $proofUrl }}'; previewTitle = 'Bukti Bayar: {{ addslashes($profile?->full_name ?: $user?->name) }}'"
                                        class="inline-flex items-center gap-1 text-violet-600 hover:text-violet-800 font-semibold text-[11px] hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span>Lihat Foto</span>
                                </button>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600 text-[11px]">
                            {{ $payment->verifier?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        @click="statusModal = true; statusActionUrl = '{{ route('admin.payments.update', $payment) }}'; currentStatus = '{{ $payment->status }}'; studentName = '{{ addslashes($profile?->full_name ?: $user?->name) }}'; rejectionReason = '{{ addslashes($payment->rejection_reason ?? '') }}'"
                                        class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">
                                    ✏️ Status
                                </button>
                                <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                                      onsubmit="return confirm('Yakin hapus data pembayaran ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">Tidak ada data pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $payments->links() }}</div>
        @endif
    </div>

    <!-- Modal Pratinjau Bukti Transfer -->
    <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
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

    <!-- Modal Form Update Status Pembayaran -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.outside="statusModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Ubah Status Pembayaran</h3>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form :action="statusActionUrl" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <p class="text-xs text-slate-600">
                    Ubah status pembayaran untuk: <strong class="text-slate-900" x-text="studentName"></strong>. Jika diubah menjadi <strong>Verified</strong>, nomor antrean tes akan otomatis diterbitkan untuk mahasiswa.
                </p>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Pilih Status Baru</label>
                    <select name="status" x-model="currentStatus" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="pending">Pending (Menunggu Verifikasi)</option>
                        <option value="verified">Verified (Disetujui & Terbitkan Antrean)</option>
                        <option value="rejected">Rejected (Ditolak / Minta Upload Ulang)</option>
                    </select>
                </div>
                <div x-show="currentStatus === 'rejected'">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Alasan Penolakan</label>
                    <textarea name="rejection_reason" x-model="rejectionReason" rows="3" placeholder="Contoh: Bukti transfer buram atau nominal kurang..."
                              class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-rose-400"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="statusModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
