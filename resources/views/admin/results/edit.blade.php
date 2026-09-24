@extends('layouts.admin')

@section('title', 'Edit Hasil Pemeriksaan')
@section('header_title', 'Edit Hasil Pemeriksaan Medis')
@section('header_subtitle', 'Ubah hasil evaluasi stasiun skrining mahasiswa')

@section('content')
<div class="max-w-xl mx-auto space-y-4">

    <a href="{{ route('admin.results.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-violet-600 transition font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke Daftar Hasil
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            @php
                $user = $stationResult->registration?->user;
                $profile = $user?->profile;
                $stationNames = [
                    'urin' => 'Stasiun 1: Tes Urin',
                    'napza' => 'Stasiun 2: Tes NAPZA',
                    'pemeriksaan' => 'Stasiun 3: Fisik & Dokter',
                ];
            @endphp
            <h2 class="text-sm font-bold text-slate-800">
                Edit Hasil: {{ $profile?->full_name ?? $user?->name ?? 'Mahasiswa' }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Stasiun: {{ $stationNames[$stationResult->station_name] ?? $stationResult->station_name }} &middot; No Antrean: {{ $stationResult->registration?->queue_code ?? '—' }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.results.update', $stationResult) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e)
                    <div>• {{ $e }}</div>
                @endforeach
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hasil Evaluasi / Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    <option value="pass" {{ $stationResult->status === 'pass' ? 'selected' : '' }}>✓ PASS (Lulus & Memenuhi Syarat)</option>
                    <option value="followup" {{ $stationResult->status === 'followup' ? 'selected' : '' }}>⚠️ FOLLOW UP (Perlu Pemeriksaan Lanjutan)</option>
                    <option value="fail" {{ $stationResult->status === 'fail' ? 'selected' : '' }}>✕ FAIL (Tidak Memenuhi Syarat)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Dokter / Petugas Pemeriksa <span class="text-red-500">*</span></label>
                <input type="text" name="examiner_name" value="{{ old('examiner_name', $stationResult->examiner_name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Medis / Hasil Laboratorium</label>
                <textarea name="notes" rows="4" placeholder="Detail parameter hasil pemeriksaan..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">{{ old('notes', $stationResult->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ route('admin.results.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
