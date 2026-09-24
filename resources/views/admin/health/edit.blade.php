@extends('layouts.admin')

@section('title', 'Edit Riwayat Kesehatan')
@section('header_title', 'Edit Riwayat Kesehatan')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <a href="{{ route('admin.health.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-violet-600 transition font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-800">Edit: {{ $healthHistory->user?->profile?->full_name ?? $healthHistory->user?->name }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.health.update', $healthHistory) }}" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Golongan Darah</label>
                    <select name="blood_type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        @foreach(['A','B','AB','O','Tidak Tahu'] as $bt)
                            <option value="{{ $bt }}" {{ $healthHistory->blood_type === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rhesus</label>
                    <select name="rhesus" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        @foreach(['+','-','Tidak Tahu'] as $r)
                            <option value="{{ $r }}" {{ $healthHistory->rhesus === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tinggi Badan (cm)</label>
                    <input type="number" name="height_cm" step="0.1" value="{{ old('height_cm', $healthHistory->height_cm) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Berat Badan (kg)</label>
                    <input type="number" name="weight_kg" step="0.1" value="{{ old('weight_kg', $healthHistory->weight_kg) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Perokok?</label>
                    <select name="is_smoker" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="0" {{ !$healthHistory->is_smoker ? 'selected' : '' }}>Tidak</option>
                        <option value="1" {{ $healthHistory->is_smoker ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Vaksin</label>
                    <input type="text" name="vaccine_status" value="{{ old('vaccine_status', $healthHistory->vaccine_status) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Kontak Darurat</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $healthHistory->emergency_contact_name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Kontak Darurat</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $healthHistory->emergency_contact_phone) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>
                @foreach([
                    ['name' => 'allergies', 'label' => 'Riwayat Alergi'],
                    ['name' => 'chronic_diseases', 'label' => 'Penyakit Kronis'],
                    ['name' => 'current_medications', 'label' => 'Obat Rutin'],
                    ['name' => 'past_surgeries', 'label' => 'Riwayat Operasi'],
                ] as $field)
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $field['label'] }}</label>
                    <textarea name="{{ $field['name'] }}" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 resize-none">{{ old($field['name'], $healthHistory->{$field['name']}) }}</textarea>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">💾 Simpan</button>
                <a href="{{ route('admin.health.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
