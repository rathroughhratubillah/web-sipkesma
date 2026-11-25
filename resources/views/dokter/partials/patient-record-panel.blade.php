{{-- Reusable Alpine-powered Patient Identity + Health History Card (expects `record` in Alpine scope) --}}
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" x-show="record" x-cloak>
    <!-- Header Banner -->
    <div class="bg-linear-to-r from-teal-700 via-teal-600 to-emerald-700 px-6 py-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-black text-2xl shrink-0">
                🩺
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">
                        Angkatan <span x-text="record.registration.academic_year"></span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                          :class="record.registration.is_verified ? 'bg-emerald-400 text-slate-900' : 'bg-amber-300 text-slate-900'"
                          x-text="record.registration.status_label"></span>
                </div>
                <h2 class="text-xl font-black tracking-tight mt-1" x-text="record.profile.full_name"></h2>
                <p class="text-xs text-teal-100 font-medium">
                    NIM/NISN: <span class="font-mono font-bold text-white" x-text="record.profile.nim"></span>
                    &bull; <span x-text="record.profile.faculty"></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-center">
            <div class="px-4 py-2 rounded-2xl bg-white text-slate-900 text-center shadow-sm min-w-[100px]">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">No. Antrean</span>
                <span class="text-xl font-black font-mono text-teal-700 leading-tight block" x-text="record.registration.queue_code"></span>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6 text-xs">
        <!-- Clinical & Verification Alerts -->
        <div class="space-y-2.5">
            <template x-if="!record.registration.is_verified">
                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center gap-3">
                    <span class="text-base shrink-0">⚠️</span>
                    <div>
                        <span class="font-bold">Catatan Status Registrasi:</span>
                        <span x-text="'Status peserta saat ini masih ' + record.registration.status_label + ' (pembayaran belum terverifikasi lunas).'"></span>
                    </div>
                </div>
            </template>

            <template x-if="!record.health.has_health">
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3">
                    <span class="text-base shrink-0">❗</span>
                    <div>
                        <span class="font-bold">Riwayat Kesehatan Belum Diisi:</span>
                        <span>Mahasiswa ini belum melengkapi formulir riwayat kesehatan di portal peserta.</span>
                    </div>
                </div>
            </template>

            <template x-if="record.health.has_allergy_alert || record.health.has_chronic_alert">
                <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 flex items-start gap-3">
                    <span class="text-lg shrink-0">🚨</span>
                    <div class="space-y-1">
                        <div class="font-black text-rose-800 uppercase tracking-wider text-[11px]">Perhatian Medis Khusus (Alergi / Penyakit Bawaan)</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-if="record.health.has_allergy_alert">
                                <div><strong>Alergi:</strong> <span x-text="record.health.allergies"></span></div>
                            </template>
                            <template x-if="record.health.has_chronic_alert">
                                <div><strong>Penyakit Kronis:</strong> <span x-text="record.health.chronic_diseases"></span></div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 1. DATA DIRI LENGKAP MAHASISWA -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-teal-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    <span>1. Data Diri Mahasiswa &amp; Jadwal Tiket</span>
                </h3>
                <span class="text-[11px] font-mono font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-lg" x-text="record.registration.barcode_code"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Nama Lengkap</span>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block" x-text="record.profile.full_name"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">NIM / NISN</span>
                    <span class="font-bold font-mono text-slate-900 text-sm mt-0.5 block" x-text="record.profile.nim"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Email Terdaftar</span>
                    <span class="font-semibold text-slate-700 mt-0.5 block truncate" x-text="record.profile.email"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Jenis Kelamin</span>
                    <span class="font-bold text-slate-800 mt-0.5 block" x-text="record.profile.gender_label"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Tanggal Lahir &amp; Usia</span>
                    <span class="font-bold text-slate-800 mt-0.5 block">
                        <span x-text="record.profile.birth_date"></span>
                        <template x-if="record.profile.age">
                            <span class="text-teal-700 font-semibold" x-text="' (' + record.profile.age + ')'"></span>
                        </template>
                    </span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">No. HP / WhatsApp</span>
                    <span class="font-bold font-mono text-teal-700 mt-0.5 block" x-text="record.profile.phone"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Fakultas</span>
                    <span class="font-bold text-slate-800 mt-0.5 block" x-text="record.profile.faculty"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Program Studi</span>
                    <span class="font-bold text-slate-800 mt-0.5 block" x-text="record.profile.major"></span>
                </div>
                <div class="p-3 rounded-2xl bg-teal-50/60 border border-teal-100">
                    <span class="text-[10px] text-teal-700 font-semibold block">Sesi Jadwal Skrining</span>
                    <span class="font-bold text-slate-900 mt-0.5 block" x-text="record.registration.session_name"></span>
                    <span class="text-[11px] text-slate-600 block" x-text="record.registration.session_date + ' • ' + record.registration.session_time"></span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2 lg:col-span-3">
                    <span class="text-[10px] text-slate-400 font-semibold block">Alamat Domisili</span>
                    <span class="font-medium text-slate-800 mt-0.5 block" x-text="record.profile.address"></span>
                </div>
            </div>
        </div>

        <!-- 2. RIWAYAT KESEHATAN LENGKAP -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>2. Riwayat Kesehatan Mahasiswa</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                      :class="record.health.has_health ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700'"
                      x-text="record.health.has_health ? '✓ Data Medis Lengkap' : 'Belum Mengisi'"></span>
            </div>

            <!-- Antropometri & Darah -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-3.5 rounded-2xl bg-rose-50/60 border border-rose-100">
                    <span class="text-[10px] text-slate-500 font-semibold block">Golongan Darah</span>
                    <span class="text-lg font-black text-rose-700 mt-0.5 block" x-text="record.health.blood_display"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-500 font-semibold block">Tinggi Badan</span>
                    <span class="text-lg font-black text-slate-900 mt-0.5 block" x-text="record.health.height_cm ? record.health.height_cm + ' cm' : '-'"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-500 font-semibold block">Berat Badan</span>
                    <span class="text-lg font-black text-slate-900 mt-0.5 block" x-text="record.health.weight_kg ? record.health.weight_kg + ' kg' : '-'"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-teal-50/70 border border-teal-100">
                    <span class="text-[10px] text-teal-700 font-semibold block">Indeks Massa Tubuh (BMI)</span>
                    <span class="text-lg font-black text-teal-800 mt-0.5 block" x-text="record.health.bmi ?? '-'"></span>
                    <span class="text-[10px] font-bold text-slate-600 block truncate" x-text="record.health.bmi_status"></span>
                </div>
            </div>

            <!-- Gaya Hidup & Vaksin -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Kebiasaan Merokok</span>
                    <span class="font-bold text-slate-800 mt-0.5 block" x-text="record.health.smoker_label"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 font-semibold block">Status Vaksinasi</span>
                    <span class="font-bold text-slate-800 mt-0.5 block" x-text="record.health.vaccine_status"></span>
                </div>
            </div>

            <!-- Anamnesis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3.5 rounded-2xl border"
                     :class="record.health.has_allergy_alert ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-100'">
                    <span class="text-[10px] text-slate-500 font-semibold block">Riwayat Alergi</span>
                    <span class="font-bold mt-0.5 block"
                          :class="record.health.has_allergy_alert ? 'text-amber-900' : 'text-slate-800'"
                          x-text="record.health.allergies"></span>
                </div>
                <div class="p-3.5 rounded-2xl border"
                     :class="record.health.has_chronic_alert ? 'bg-rose-50 border-rose-200' : 'bg-slate-50 border-slate-100'">
                    <span class="text-[10px] text-slate-500 font-semibold block">Penyakit Kronis / Bawaan</span>
                    <span class="font-bold mt-0.5 block"
                          :class="record.health.has_chronic_alert ? 'text-rose-900' : 'text-slate-800'"
                          x-text="record.health.chronic_diseases"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-500 font-semibold block">Obat yang Rutin Dikonsumsi</span>
                    <span class="font-medium text-slate-800 mt-0.5 block" x-text="record.health.current_medications"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-500 font-semibold block">Riwayat Operasi / Tindakan Medis</span>
                    <span class="font-medium text-slate-800 mt-0.5 block" x-text="record.health.past_surgeries"></span>
                </div>
            </div>

            <!-- Kontak Darurat -->
            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-[10px] text-teal-800 font-bold uppercase tracking-wider block">Kontak Darurat Keluarga</span>
                    <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="record.health.emergency_contact_name"></span>
                </div>
                <div class="sm:text-right">
                    <span class="text-[10px] text-slate-500 font-semibold block">Nomor Telepon / WhatsApp</span>
                    <span class="text-sm font-mono font-black text-teal-800 mt-0.5 block" x-text="record.health.emergency_contact_phone"></span>
                </div>
            </div>
        </div>

        <!-- 3. PROGRES 3 STASIUN -->
        <div class="space-y-3">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span>3. Status Rangkaian Stasiun Pemeriksaan</span>
                </h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <template x-for="st in record.stations" :key="st.key">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-800" x-text="st.title"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                  :class="st.status === 'pass' ? 'bg-emerald-100 text-emerald-700' : (st.status === 'fail' ? 'bg-rose-100 text-rose-700' : (st.status === 'followup' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-600'))"
                                  x-text="st.status === 'pass' ? 'Lulus' : (st.status === 'pending' ? 'Belum' : st.status)"></span>
                        </div>
                        <template x-if="st.notes">
                            <p class="text-[11px] text-slate-600 leading-snug" x-text="st.notes"></p>
                        </template>
                        <template x-if="st.examiner_name">
                            <p class="text-[10px] text-slate-400" x-text="'Pemeriksa: ' + st.examiner_name"></p>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 print:hidden">
            <div class="flex flex-wrap items-center gap-2">
                <template x-if="record.registration.status === 'cleared'">
                    <form :action="record.registration.checkin_url" method="POST"
                          @submit.prevent="if (typeof confirmOnSiteCheckin === 'function') { confirmOnSiteCheckin(record.registration.checkin_url); } else { $el.submit(); }">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>✓ Konfirmasi Hadir (Check-in Di Tempat)</span>
                        </button>
                    </form>
                </template>
                <template x-if="record.registration.checked_in_at">
                    <span class="px-3 py-2 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 font-semibold text-xs">
                        ✓ Check-in tercatat: <span class="font-mono font-bold" x-text="record.registration.checked_in_at"></span>
                    </span>
                </template>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                    🖨️ Cetak Ringkasan Medis
                </button>
            </div>
        </div>
    </div>
</div>

