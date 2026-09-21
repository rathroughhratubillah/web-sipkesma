@extends('layouts.staff')

@section('title', 'Rekap Data Peserta PPKMB')
@section('header_title', 'Rekapitulasi Data Peserta PPKMB')

@section('content')
<div class="space-y-6">

    <!-- Filters & Export Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('staff.participants.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-4">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari NIM, Nama, No HP, No Antrean..."
                       class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium outline-hidden">
            </div>

            <!-- Filter Status -->
            <div class="lg:col-span-3">
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-semibold bg-white outline-hidden">
                    <option value="all">-- Semua Status Pendaftaran --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Belum Selesai)</option>
                    <option value="awaiting_payment" {{ $status === 'awaiting_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="awaiting_verification" {{ $status === 'awaiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="cleared" {{ $status === 'cleared' ? 'selected' : '' }}>Cleared (Tiket Terbit)</option>
                    <option value="checked_in" {{ $status === 'checked_in' ? 'selected' : '' }}>Hadir di Lokasi</option>
                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Sedang Diperiksa</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed (Lulus & Bersertifikat)</option>
                </select>
            </div>

            <!-- Filter Fakultas -->
            <div class="lg:col-span-3">
                <select name="faculty" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-semibold bg-white outline-hidden">
                    <option value="all">-- Semua Fakultas --</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac }}" {{ $faculty === $fac ? 'selected' : '' }}>{{ $fac }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition">
                    Filter
                </button>
                <a href="{{ route('staff.participants.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold">
                    Reset
                </a>
            </div>
        </form>

        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
            <span class="text-xs text-slate-500">Menampilkan <strong>{{ $participants->total() }}</strong> mahasiswa terdaftar</span>

            <!-- Export to CSV -->
            <a href="{{ route('staff.participants.export') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                <span>Ekspor Rekap CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Participants Master Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">Antrean</th>
                        <th class="p-3.5">NIM & Mahasiswa</th>
                        <th class="p-3.5">Fakultas / Prodi</th>
                        <th class="p-3.5">Sesi Skrining</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Tes Urin</th>
                        <th class="p-3.5">NAPZA</th>
                        <th class="p-3.5">Fisik</th>
                        <th class="p-3.5">Sertifikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($participants as $reg)
                        @php
                            $u = $reg->user;
                            $prof = $u?->profile;
                            $health = $u?->healthHistory;
                            $urin = $reg->getStationResult('urin');
                            $napza = $reg->getStationResult('napza');
                            $fisik = $reg->getStationResult('pemeriksaan');
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5">
                                <span class="font-bold font-mono text-teal-900 bg-teal-50 px-2 py-0.5 rounded-md">
                                    {{ $reg->queue_code ?: '—' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">{{ $prof?->full_name ?: $u?->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $prof?->nim ?: $u?->email }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="text-slate-800 font-semibold">{{ $prof?->faculty ?: '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $prof?->major ?: '-' }}</div>
                            </td>
                            <td class="p-3.5 text-slate-600">
                                {{ $reg->testSession?->session_name ?: '-' }}
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $reg->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                       ($reg->status === 'cleared' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 
                                       ($reg->status === 'awaiting_verification' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $reg->status }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urin?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($urin ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                    {{ $urin?->status ?: '—' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $napza?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($napza ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                    {{ $napza?->status ?: '—' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $fisik?->status === 'pass' ? 'bg-emerald-100 text-emerald-800' : ($fisik ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-400') }}">
                                    {{ $fisik?->status ?: '—' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($reg->status === 'completed')
                                    <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-1">
                                        ✓ Terbit
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Belum</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Tidak ada data peserta yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($participants->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $participants->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
