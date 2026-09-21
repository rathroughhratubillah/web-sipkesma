@extends('layouts.student')

@section('title', 'Riwayat Kesehatan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Riwayat Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-1">Data medis ini digunakan oleh tim dokter untuk skrining awal kesehatan PPKMB.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 3 dari 11
        </span>
    </div>

    <!-- Health Form Card with Alpine.js live BMI Calculation -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm"
         x-data="{
            height: '{{ old('height_cm', $health->height_cm ?? 165) }}',
            weight: '{{ old('weight_kg', $health->weight_kg ?? 55) }}',
            get bmi() {
                let h = parseFloat(this.height);
                let w = parseFloat(this.weight);
                if (h > 50 && w > 20) {
                    let m = h / 100;
                    return (w / (m * m)).toFixed(1);
                }
                return '-';
            },
            get bmiStatus() {
                let b = parseFloat(this.bmi);
                if (isNaN(b)) return { text: '-', color: 'bg-slate-100 text-slate-600' };
                if (b < 18.5) return { text: 'Kurus (Underweight)', color: 'bg-amber-50 text-amber-700 border border-amber-200' };
                if (b <= 22.9) return { text: 'Normal (Ideal)', color: 'bg-emerald-50 text-emerald-700 border border-emerald-200' };
                if (b <= 24.9) return { text: 'Kelebihan Berat Badan', color: 'bg-amber-50 text-amber-700 border border-amber-200' };
                return { text: 'Obesitas', color: 'bg-rose-50 text-rose-700 border border-rose-200' };
            }
         }">
        
        <form method="POST" action="{{ route('student.health_history.store') }}" class="space-y-6">
            @csrf

            <!-- Golongan Darah & Rhesus -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Golongan Darah <span class="text-rose-500">*</span>
                    </label>
                    <select name="blood_type" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition bg-white">
                        @foreach(['A', 'B', 'AB', 'O', 'Tidak Tahu'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type', $health->blood_type ?? '') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Rhesus Darah <span class="text-rose-500">*</span>
                    </label>
                    <select name="rhesus" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition bg-white">
                        <option value="+" {{ old('rhesus', $health->rhesus ?? '+') === '+' ? 'selected' : '' }}>Positif (+)</option>
                        <option value="-" {{ old('rhesus', $health->rhesus ?? '') === '-' ? 'selected' : '' }}>Negatif (-)</option>
                        <option value="Tidak Tahu" {{ old('rhesus', $health->rhesus ?? '') === 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                    </select>
                </div>
            </div>

            <!-- Tinggi, Berat, Kalkulator BMI Otomatis -->
            <div class="p-5 rounded-2xl bg-teal-50/50 border border-teal-100 space-y-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-xs font-bold text-teal-900 uppercase tracking-wider">Kalkulasi Indeks Massa Tubuh (BMI) Otomatis</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tinggi Badan (cm)</label>
                        <input type="number" step="0.5" name="height_cm" x-model="height" required
                               placeholder="165"
                               class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 focus:border-teal-500 text-sm font-semibold outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Berat Badan (kg)</label>
                        <input type="number" step="0.5" name="weight_kg" x-model="weight" required
                               placeholder="55"
                               class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 focus:border-teal-500 text-sm font-semibold outline-hidden">
                    </div>

                    <div class="bg-white p-3 rounded-xl border border-teal-100 flex flex-col justify-center text-center">
                        <div class="text-[11px] font-medium text-slate-500">Skor BMI Anda</div>
                        <div class="text-xl font-extrabold text-teal-700 font-mono" x-text="bmi"></div>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" 
                                  :class="bmiStatus.color" 
                                  x-text="bmiStatus.text"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alergi & Riwayat Penyakit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Riwayat Alergi (Makanan / Obat / Cuaca)
                    </label>
                    <input type="text" name="allergies" value="{{ old('allergies', $health->allergies ?? '') }}"
                           placeholder="Contoh: Alergi udang, penisilin (atau kosongkan jika tidak ada)"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Penyakit Kronis / Bawaan
                    </label>
                    <input type="text" name="chronic_diseases" value="{{ old('chronic_diseases', $health->chronic_diseases ?? '') }}"
                           placeholder="Contoh: Asma, maag kronis, jantung (atau kosongkan)"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                </div>
            </div>

            <!-- Obat Rutin & Riwayat Operasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Obat-obatan yang Sedang Dikonsumsi
                    </label>
                    <input type="text" name="current_medications" value="{{ old('current_medications', $health->current_medications ?? '') }}"
                           placeholder="Nama obat rutin jika ada"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Riwayat Tindakan Bedah / Operasi
                    </label>
                    <input type="text" name="past_surgeries" value="{{ old('past_surgeries', $health->past_surgeries ?? '') }}"
                           placeholder="Contoh: Operasi usus buntu tahun 2023 (atau kosongkan)"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                </div>
            </div>

            <!-- Status Merokok & Vaksin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kebiasaan Merokok / Vape <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-teal-500 transition">
                            <input type="radio" name="is_smoker" value="0" {{ old('is_smoker', $health->is_smoker ?? false) ? '' : 'checked' }} required class="text-teal-600 focus:ring-teal-500">
                            <span class="text-sm font-medium text-slate-700">Tidak Merokok</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-teal-500 transition">
                            <input type="radio" name="is_smoker" value="1" {{ old('is_smoker', $health->is_smoker ?? false) ? 'checked' : '' }} required class="text-teal-600 focus:ring-teal-500">
                            <span class="text-sm font-medium text-slate-700">Ya, Merokok/Vape</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Vaksinasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="vaccine_status" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-sm outline-hidden transition bg-white">
                        <option value="Lengkap (Dosis 3/Booster)" {{ old('vaccine_status', $health->vaccine_status ?? '') === 'Lengkap (Dosis 3/Booster)' ? 'selected' : '' }}>Lengkap (Dosis 3/Booster)</option>
                        <option value="Dosis 2 Primer" {{ old('vaccine_status', $health->vaccine_status ?? '') === 'Dosis 2 Primer' ? 'selected' : '' }}>Dosis 2 Primer</option>
                        <option value="Dosis 1" {{ old('vaccine_status', $health->vaccine_status ?? '') === 'Dosis 1' ? 'selected' : '' }}>Dosis 1</option>
                        <option value="Belum Vaksin" {{ old('vaccine_status', $health->vaccine_status ?? '') === 'Belum Vaksin' ? 'selected' : '' }}>Belum Vaksin (Alasan Medis)</option>
                    </select>
                </div>
            </div>

            <!-- Kontak Darurat -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-4">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Kontak Darurat (Orang Tua / Wali)</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kontak Darurat <span class="text-rose-500">*</span></label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $health->emergency_contact_name ?? '') }}" required
                               placeholder="Nama Ayah/Ibu/Wali"
                               class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Telepon Darurat <span class="text-rose-500">*</span></label>
                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $health->emergency_contact_phone ?? '') }}" required
                               placeholder="Contoh: 081277889900"
                               class="w-full px-4 py-2 rounded-xl bg-white border border-slate-200 focus:border-teal-500 text-sm outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('student.profile') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    &larr; Data Diri
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                    <span>Simpan & Pilih Jadwal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
