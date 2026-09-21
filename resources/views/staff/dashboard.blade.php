@extends('layouts.staff')

@section('title', 'Dashboard Petugas')
@section('header_title', 'Ringkasan Pelayanan Skrining Medis')

@section('content')
<div class="space-y-6">

    <!-- Top Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Mahasiswa -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Terdaftar</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_students'] }}</div>
                <span class="text-[11px] text-teal-600 font-medium">Mahasiswa Baru PPKMB</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
            </div>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Perlu Verifikasi</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending_payments'] }}</div>
                <a href="{{ route('staff.payments.index') }}" class="text-[11px] text-amber-600 hover:underline font-semibold">Tinjau Bukti Bayar &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Hadir Check-In -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir di Lokasi</span>
                <div class="text-2xl font-black text-teal-600 mt-1">{{ $stats['checked_in'] }}</div>
                <span class="text-[11px] text-slate-500">Dalam antrean / periksa</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Selesai & Lulus -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai & Lulus</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['completed'] }}</div>
                <span class="text-[11px] text-emerald-600 font-semibold">Sertifikat Terbit</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
            </div>
        </div>

    </div>

    <!-- Quick Actions Bar -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-teal-700 to-teal-800 text-white shadow-md flex flex-wrap items-center justify-between gap-4">
        <div class="space-y-0.5">
            <h2 class="text-base font-bold">Akses Cepat Meja Layanan</h2>
            <p class="text-xs text-teal-200">Gunakan pintasan di samping untuk melayani alur peserta secara langsung.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('staff.payments.index') }}" class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-md transition">
                💳 Verifikasi Bayar
            </a>
            <a href="{{ route('staff.checkin.index') }}" class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-md transition">
                📷 Scanner Check-in
            </a>
            <a href="{{ route('staff.queue.display') }}" class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-md transition">
                📢 Layar Antrean
            </a>
            <a href="{{ route('staff.station.index', 'urin') }}" class="px-3.5 py-2 rounded-xl bg-teal-400 text-slate-900 text-xs font-bold shadow-sm hover:bg-teal-300 transition">
                🩺 Input 3 Stasiun Tes
            </a>
        </div>
    </div>

    <!-- Two Columns: Recent Registrations & Active Sesi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Recent Registrations (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Aktivitas Pendaftaran Terbaru</h3>
                    <p class="text-xs text-slate-500">Mahasiswa yang baru memperbarui data atau status skrining</p>
                </div>
                <a href="{{ route('staff.participants.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat Semua Peserta &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">No Antrean</th>
                            <th class="p-3">Mahasiswa</th>
                            <th class="p-3">Fakultas</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentRegistrations as $reg)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3">
                                    <span class="font-bold font-mono {{ $reg->queue_code ? 'text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md' : 'text-slate-400' }}">
                                        {{ $reg->queue_code ?: '—' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="font-bold text-slate-900">{{ $reg->user?->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $reg->user?->profile?->nim ?: $reg->user?->email }}</div>
                                </td>
                                <td class="p-3 text-slate-600">
                                    {{ $reg->user?->profile?->faculty ?: 'Belum diisi' }}
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                                        {{ $reg->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                           ($reg->status === 'cleared' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 
                                           ($reg->status === 'awaiting_verification' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600')) }}">
                                        {{ $reg->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    @if($reg->status === 'awaiting_verification')
                                        <a href="{{ route('staff.payments.index') }}" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-[11px]">
                                            Verifikasi
                                        </a>
                                    @elseif(in_array($reg->status, ['checked_in', 'in_progress']))
                                        <a href="{{ route('staff.station.index', 'urin') }}" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[11px]">
                                            Input Tes
                                        </a>
                                    @else
                                        <a href="{{ route('staff.participants.index', ['search' => $reg->queue_code ?: $reg->user?->name]) }}" class="text-teal-600 hover:underline">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada aktivitas pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Test Sessions (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Sesi Skrining Aktif</h3>
                    <p class="text-xs text-slate-500">Pantauan sisa kuota per sesi</p>
                </div>
                <a href="{{ route('staff.sessions.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Kelola</a>
            </div>

            <div class="space-y-3">
                @forelse($activeSessions as $s)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/60 space-y-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-xs font-bold text-slate-800">{{ $s->session_name }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ \Carbon\Carbon::parse($s->session_date)->locale('id')->isoFormat('D MMM Y') }} &middot; {{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->remaining_quota > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $s->remaining_quota }} Sisa
                            </span>
                        </div>

                        <!-- Progress bar -->
                        @php $percent = $s->quota > 0 ? min(100, round(($s->registrations_count / $s->quota) * 100)) : 0; @endphp
                        <div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-teal-600" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                                <span>Terisi: {{ $s->registrations_count }}</span>
                                <span>Kapasitas: {{ $s->quota }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">Tidak ada sesi aktif.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
