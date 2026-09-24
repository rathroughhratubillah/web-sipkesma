@extends('layouts.admin')

@section('title', 'Edit Registrasi')
@section('header_title', 'Edit Data Registrasi')

@section('content')
<div class="max-w-xl mx-auto space-y-4">

    <a href="{{ route('admin.registrations.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-violet-600 transition font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-800">
                Edit Registrasi: {{ $registration->user?->profile?->full_name ?? $registration->user?->name }}
            </h2>
            <p class="text-xs text-slate-500">No Antrean: {{ $registration->queue_code ?? '—' }}</p>
        </div>

        <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Registrasi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $registration->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sesi Jadwal</label>
                <select name="test_session_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                    <option value="">— Belum dipilih —</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->id }}" {{ $registration->test_session_id == $sess->id ? 'selected' : '' }}>
                            {{ $sess->session_name }} — {{ \Carbon\Carbon::parse($sess->session_date)->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Antrean</label>
                <input type="text" name="queue_code" value="{{ old('queue_code', $registration->queue_code) }}" placeholder="Cth: A-001"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">💾 Simpan</button>
                <a href="{{ route('admin.registrations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
