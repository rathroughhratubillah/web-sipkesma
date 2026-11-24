@extends('layouts.admin')

@section('title', 'Edit Riwayat Kesehatan Mahasiswa')
@section('header_title', 'Edit Riwayat Kesehatan Mahasiswa')
@section('header_subtitle', 'Perbarui data identitas & riwayat kesehatan mahasiswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.health.index', ['faculty' => $profile->faculty, 'major' => $profile->major]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span>Kembali ke Tabel Mahasiswa</span>
        </a>

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('admin.health.index') }}" class="hover:text-teal-600 transition">Riwayat Kesehatan</a>
            @if($profile->faculty)
                <span>/</span>
                <a href="{{ route('admin.health.index', ['faculty' => $profile->faculty]) }}" class="hover:text-teal-600 transition">{{ $profile->faculty }}</a>
            @endif
            @if($profile->major)
                <span>/</span>
                <span class="text-teal-700 font-bold">{{ $profile->major }}</span>
            @endif
        </nav>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden"
         x-data="{
             facultyMap: {{ json_encode($faculties) }},
             selectedFaculty: '{{ old('faculty', $profile->faculty ?? array_key_first($faculties)) }}',
             selectedMajor: '{{ old('major', $profile->major ?? '') }}',
             get currentMajors() {
                 return this.facultyMap[this.selectedFaculty] || [];
             }
         }">

        <div class="p-6 bg-linear-to-r from-teal-600 to-emerald-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-black text-xl text-white">
                    🩺
                </div>
                <div>
                    <h2 class="text-lg font-black tracking-tight">
                        {{ $profile->full_name ?: $user->name }}
                    </h2>
                    <p class="text-xs text-teal-100 font-medium">
                        NIM: <span class="font-mono font-bold">{{ $profile->nim ?: '-' }}</span> • Email: {{ $user->email }}
                    </p>
                </div>
            </div>

            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $healthHistory->exists ? 'bg-emerald-500/30 text-emerald-100 border border-emerald-400/40' : 'bg-amber-500/30 text-amber-100 border border-amber-400/40' }}">
                {{ $healthHistory->exists ? 'Sudah Ada Riwayat' : 'Formulir Baru' }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.health.update', $user->id) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <div class="font-bold">Terdapat beberapa kesalahan pengisian:</div>
                    @foreach($errors->all() as $e)
                        <div>• {{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <!-- SEKSI 1: IDENTITAS MAHASISWA -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs font-bold flex items-center justify-center">1</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Identitas Mahasiswa & Unit Akademik</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Akun User <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap (Sesuai KTP/Ijazah) <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIM / NISN <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" value="{{ old('nim', $profile->nim) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-mono font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fakultas <span class="text-rose-500">*</span></label>
                        <select name="faculty" x-model="selectedFaculty" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            @foreach(array_keys($faculties) as $fac)
                                <option value="{{ $fac }}">{{ $fac }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Program Studi / Jurusan <span class="text-rose-500">*</span></label>
                        <select name="major" x-model="selectedMajor" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            <option value="">-- Pilih Program Studi --</option>
                            <template x-for="maj in currentMajors" :key="maj">
                                <option :value="maj" x-text="maj" :selected="maj === selectedMajor"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select name="gender" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            <option value="L" {{ old('gender', $profile->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $profile->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $profile->birth_date ? date('Y-m-d', strtotime($profile->birth_date)) : '') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili</label>
                        <input type="text" name="address" value="{{ old('address', $profile->address) }}"
                               placeholder="Alamat lengkap tempat tinggal sekarang..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>
                </div>
            </div>

            <!-- SEKSI 2: RIWAYAT KESEHATAN & FISIK -->
            <div class="space-y-4 pt-4 border-t border-slate-200/80">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold flex items-center justify-center">2</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Pemeriksaan Fisik & Riwayat Medis</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Golongan Darah <span class="text-rose-500">*</span></label>
                        <select name="blood_type" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            @foreach(['A','B','AB','O','Tidak Tahu'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type', $healthHistory->blood_type) === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Rhesus <span class="text-rose-500">*</span></label>
                        <select name="rhesus" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            @foreach(['+','-','Tidak Tahu'] as $r)
                                <option value="{{ $r }}" {{ old('rhesus', $healthHistory->rhesus) === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" name="height_cm" value="{{ old('height_cm', $healthHistory->height_cm) }}" required placeholder="Contoh: 170"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg', $healthHistory->weight_kg) }}" required placeholder="Contoh: 60"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Perokok? <span class="text-rose-500">*</span></label>
                        <select name="is_smoker" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                            <option value="0" {{ old('is_smoker', $healthHistory->is_smoker) == 0 ? 'selected' : '' }}>Bukan Perokok</option>
                            <option value="1" {{ old('is_smoker', $healthHistory->is_smoker) == 1 ? 'selected' : '' }}>Perokok Aktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Vaksinasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="vaccine_status" value="{{ old('vaccine_status', $healthHistory->vaccine_status ?? 'Dosis 3 (Booster)') }}" required
                               placeholder="Contoh: Dosis 3 (Booster)"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kontak Darurat <span class="text-rose-500">*</span></label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $healthHistory->emergency_contact_name) }}" required
                               placeholder="Nama Orang Tua / Wali / Saudara..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. HP Kontak Darurat <span class="text-rose-500">*</span></label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $healthHistory->emergency_contact_phone) }}" required
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Riwayat Alergi (Obat / Makanan / Debu)</label>
                        <textarea name="allergies" rows="2" placeholder="Kosongkan atau ketik 'Tidak ada'..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden resize-none">{{ old('allergies', $healthHistory->allergies) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penyakit Kronis / Bawaan</label>
                        <textarea name="chronic_diseases" rows="2" placeholder="Asma, Jantung, Maag akut, dsb. Ketik 'Tidak ada' jika sehat..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden resize-none">{{ old('chronic_diseases', $healthHistory->chronic_diseases) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Obat-obatan Rutin Yang Sedang Dikonsumsi</label>
                        <textarea name="current_medications" rows="2" placeholder="Sebutkan nama obat rutin jika ada..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden resize-none">{{ old('current_medications', $healthHistory->current_medications) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Riwayat Operasi / Rawat Inap</label>
                        <textarea name="past_surgeries" rows="2" placeholder="Operasi usus buntu, patah tulang, dsb..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-medium bg-white outline-hidden resize-none">{{ old('past_surgeries', $healthHistory->past_surgeries) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.health.index', ['faculty' => $profile->faculty, 'major' => $profile->major]) }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span>Simpan Perubahan Riwayat Kesehatan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
