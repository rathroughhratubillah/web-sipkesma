@extends('layouts.admin')

@section('title', 'Edit Profil Mahasiswa')
@section('header_title', 'Edit Profil Mahasiswa')
@section('header_subtitle', 'Ubah data profil yang disimpan di database')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <a href="{{ route('admin.profiles.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-violet-600 transition font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke Daftar Profil
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-800">Edit: {{ $profile->full_name }}</h2>
            <p class="text-xs text-slate-500">Akun: {{ $profile->user?->email }}</p>
        </div>

        <form method="POST" action="{{ route('admin.profiles.update', $profile) }}" class="p-6 space-y-5" 
              x-data="{
                faculties: @json(array_keys($faculties)),
                majors: @json($faculties),
                selectedFaculty: '{{ old('faculty', $profile->faculty) }}',
                get filteredMajors() {
                    return this.majors[this.selectedFaculty] || [];
                }
              }">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">NISN <span class="text-red-500">*</span></label>
                    <input type="text" name="nim" value="{{ old('nim', $profile->nim) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fakultas <span class="text-red-500">*</span></label>
                    <select name="faculty" x-model="selectedFaculty" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach(array_keys($faculties) as $fac)
                            <option value="{{ $fac }}" {{ old('faculty', $profile->faculty) === $fac ? 'selected' : '' }}>{{ $fac }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Program Studi <span class="text-red-500">*</span></label>
                    <select name="major" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="">-- Pilih Program Studi --</option>
                        <template x-for="m in filteredMajors" :key="m">
                            <option :value="m" :selected="m === '{{ old('major', $profile->major) }}'" x-text="m"></option>
                        </template>
                        {{-- Fallback jika JS tidak jalan --}}
                        @if($profile->major)
                            <option value="{{ $profile->major }}" selected>{{ $profile->major }}</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="L" {{ $profile->gender === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $profile->gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $profile->birth_date?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 resize-none">{{ old('address', $profile->address) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                    💾 Simpan
                </button>
                <a href="{{ route('admin.profiles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
