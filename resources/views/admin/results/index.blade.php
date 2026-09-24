@extends('layouts.admin')

@section('title', 'Hasil Pemeriksaan Medis')
@section('header_title', 'Kelola Hasil Pemeriksaan Medis')
@section('header_subtitle', 'Semua data hasil pemeriksaan 3 stasiun (Urin, NAPZA, Fisik) di database')

@section('content')
<div class="space-y-4">

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Mahasiswa</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama, NISN, atau no antrean..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Stasiun Medis</label>
            <select name="station" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Stasiun</option>
                <option value="urin" {{ $station === 'urin' ? 'selected' : '' }}>Stasiun 1: Tes Urin</option>
                <option value="napza" {{ $station === 'napza' ? 'selected' : '' }}>Stasiun 2: Tes NAPZA</option>
                <option value="pemeriksaan" {{ $station === 'pemeriksaan' ? 'selected' : '' }}>Stasiun 3: Fisik & Dokter</option>
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Hasil</label>
            <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Hasil</option>
                <option value="pass" {{ $status === 'pass' ? 'selected' : '' }}>Pass (Lulus)</option>
                <option value="followup" {{ $status === 'followup' ? 'selected' : '' }}>Followup (Evaluasi)</option>
                <option value="fail" {{ $status === 'fail' ? 'selected' : '' }}>Fail (Tidak Lulus)</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">🔍 Cari</button>
        <a href="{{ route('admin.results.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Daftar Hasil Pemeriksaan Stasiun</h2>
            <p class="text-xs text-slate-400">Total: {{ $results->total() }} catatan pemeriksaan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">No Antrean</th>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">Stasiun Pemeriksaan</th>
                        <th class="px-5 py-3">Hasil / Status</th>
                        <th class="px-5 py-3">Catatan / Keterangan Medis</th>
                        <th class="px-5 py-3">Pemeriksa</th>
                        <th class="px-5 py-3">Waktu Periksa</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($results as $res)
                    @php
                        $reg = $res->registration;
                        $user = $reg?->user;
                        $profile = $user?->profile;
                        $stationLabels = [
                            'urin' => 'Stasiun 1: Tes Urin',
                            'napza' => 'Stasiun 2: Tes NAPZA',
                            'pemeriksaan' => 'Stasiun 3: Fisik & Dokter',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <span class="font-bold font-mono text-teal-700 bg-teal-50 px-2 py-0.5 rounded-lg">
                                {{ $reg?->queue_code ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $profile?->full_name ?? $user?->name ?? '—' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $profile?->nim ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-semibold text-slate-700">
                                {{ $stationLabels[$res->station_name] ?? $res->station_name }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($res->status === 'pass')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    ✓ PASS (Lulus)
                                </span>
                            @elseif($res->status === 'followup')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    ⚠️ Follow Up
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    ✕ FAIL
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600 max-w-[240px] truncate" title="{{ $res->notes }}">
                            {{ $res->notes ?: '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-700 font-medium">
                            {{ $res->examiner_name ?: '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-500">
                            {{ $res->examined_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.results.edit', $res) }}"
                                   class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.results.destroy', $res) }}"
                                      onsubmit="return confirm('Yakin hapus data hasil pemeriksaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">Tidak ada data hasil pemeriksaan ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $results->links() }}</div>
        @endif
    </div>

</div>
@endsection
