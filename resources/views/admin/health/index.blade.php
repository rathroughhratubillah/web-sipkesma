@extends('layouts.admin')

@section('title', 'Riwayat Kesehatan')
@section('header_title', 'Kelola Riwayat Kesehatan')
@section('header_subtitle', 'Data kesehatan mahasiswa yang tersimpan di database')

@section('content')
<div class="space-y-4">

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Nama atau NISN mahasiswa..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">🔍 Cari</button>
        <a href="{{ route('admin.health.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Data Riwayat Kesehatan</h2>
            <p class="text-xs text-slate-400">Total: {{ $healthHistories->total() }} data</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">Gol. Darah</th>
                        <th class="px-5 py-3">TB / BB</th>
                        <th class="px-5 py-3">BMI</th>
                        <th class="px-5 py-3">Perokok</th>
                        <th class="px-5 py-3">Vaksin</th>
                        <th class="px-5 py-3">Kontak Darurat</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($healthHistories as $h)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $h->user?->profile?->full_name ?? $h->user?->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $h->user?->profile?->nim ?? $h->user?->email }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-lg bg-red-100 text-red-700 font-bold text-[11px]">
                                {{ $h->blood_type }}{{ $h->rhesus }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $h->height_cm }} cm / {{ $h->weight_kg }} kg</td>
                        <td class="px-5 py-3">
                            <div class="font-bold text-slate-800">{{ $h->bmi }}</div>
                            <div class="text-[10px] text-slate-400">{{ $h->bmi_status }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $h->is_smoker ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $h->is_smoker ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $h->vaccine_status }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-slate-700">{{ $h->emergency_contact_name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $h->emergency_contact_phone }}</div>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.health.edit', $h) }}"
                                   class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.health.destroy', $h) }}"
                                      onsubmit="return confirm('Yakin hapus data kesehatan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">Tidak ada data kesehatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($healthHistories->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $healthHistories->links() }}</div>
        @endif
    </div>
</div>
@endsection
