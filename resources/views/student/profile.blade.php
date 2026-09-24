@extends('layouts.student')

@section('title', 'Form Data Diri')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Data Diri Mahasiswa</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi identitas diri Anda sesuai data registrasi akademik kampus.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 2 dari 11
        </span>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm" 
         x-data="{
            faculty: '{{ old('faculty', $profile->faculty ?? '') }}',
            faculties: {{ json_encode($faculties) }},
            selectedMajor: '{{ old('major', $profile->major ?? '') }}'
         }">
        
        <form method="POST" action="{{ route('student.profile.store') }}" class="space-y-6">
            @csrf

            <!-- NISN & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Induk Siswa Nasional (NISN) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nim" value="{{ old('nim', $profile->nim ?? '') }}" required
                           placeholder="Contoh: 1234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                    @error('nim') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? $user->name) }}" required
                           placeholder="Nama sesuai KTP / Akta"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                    @error('full_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Fakultas & Program Studi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Fakultas <span class="text-rose-500">*</span>
                    </label>
                    <select name="faculty" x-model="faculty" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition bg-white">
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach(array_keys($faculties) as $fac)
                            <option value="{{ $fac }}">{{ $fac }}</option>
                        @endforeach
                    </select>
                    @error('faculty') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Program Studi <span class="text-rose-500">*</span>
                    </label>
                    <select name="major" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition bg-white">
                        <option value="">-- Pilih Program Studi --</option>
                        <template x-for="item in (faculties[faculty] || [])" :key="item">
                            <option :value="item" x-text="item" :selected="item === selectedMajor"></option>
                        </template>
                    </select>
                    @error('major') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Gender & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-teal-500 transition">
                            <input type="radio" name="gender" value="L" {{ old('gender', $profile->gender ?? '') === 'L' ? 'checked' : '' }} required class="text-teal-600 focus:ring-teal-500">
                            <span class="text-sm font-medium text-slate-700">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-teal-500 transition">
                            <input type="radio" name="gender" value="P" {{ old('gender', $profile->gender ?? '') === 'P' ? 'checked' : '' }} required class="text-teal-600 focus:ring-teal-500">
                            <span class="text-sm font-medium text-slate-700">Perempuan</span>
                        </label>
                    </div>
                    @error('gender') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $profile?->birth_date?->format('Y-m-d') ?? '2007-01-01') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                    @error('birth_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- No HP & Alamat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                </label>
                <input type="tel" name="phone" value="{{ old('phone', $profile->phone ?? '') }}" required
                       placeholder="Contoh: 081234567890"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Domisili Sekarang <span class="text-rose-500">*</span>
                </label>
                <textarea name="address" rows="3" required
                          placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota"
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">{{ old('address', $profile->address ?? '') }}</textarea>
                @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('student.beranda') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    &larr; Kembali ke Beranda
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                    <span>Simpan & Lanjutkan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
