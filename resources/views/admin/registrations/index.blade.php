@extends('layouts.admin')

@section('title', 'Data Registrasi')
@section('header_title', 'Kelola Data Registrasi')
@section('header_subtitle', 'Semua data pendaftaran mahasiswa yang tersimpan')

@section('content')
<div class="space-y-4">

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama, NISN, atau no antrean..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <div class="min-w-[180px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Status</option>
                @foreach(['draft','awaiting_payment','awaiting_verification','cleared','checked_in','in_progress','completed'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">🔍 Cari</button>
        <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Data Registrasi</h2>
            <p class="text-xs text-slate-400">Total: {{ $registrations->total() }} data</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">No Antrean</th>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">Sesi Jadwal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Check-in</th>
                        <th class="px-5 py-3">Selesai</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $reg)
                    @php
                    $statusColors = [
                        'draft'                  => 'bg-slate-100 text-slate-600',
                        'awaiting_payment'       => 'bg-amber-100 text-amber-700',
                        'awaiting_verification'  => 'bg-orange-100 text-orange-700',
                        'cleared'                => 'bg-teal-100 text-teal-700',
                        'checked_in'             => 'bg-blue-100 text-blue-700',
                        'in_progress'            => 'bg-purple-100 text-purple-700',
                        'completed'              => 'bg-emerald-100 text-emerald-700',
                    ];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <span class="font-bold font-mono {{ $reg->queue_code ? 'text-teal-700 bg-teal-50 px-2 py-0.5 rounded-lg' : 'text-slate-400' }}">
                                {{ $reg->queue_code ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $reg->user?->profile?->full_name ?? $reg->user?->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $reg->user?->profile?->nim ?? $reg->user?->email }}</div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ $reg->testSession?->session_name ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusColors[$reg->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $reg->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $reg->checked_in_at?->format('d/m H:i') ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $reg->completed_at?->format('d/m H:i') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.registrations.edit', $reg) }}"
                                   class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.registrations.destroy', $reg) }}"
                                      onsubmit="return confirm('Yakin hapus registrasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Tidak ada data registrasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $registrations->links() }}</div>
        @endif
    </div>
</div>
@endsection
