@extends('layouts.student')

@section('title', 'Konfirmasi Pendaftaran')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tinjau & Konfirmasi Data</h1>
            <p class="text-sm text-slate-500 mt-1">Pastikan seluruh data yang Anda masukkan sudah benar sebelum melanjutkan ke pembayaran.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 5 dari 11
        </span>
    </div>

    <!-- Summary Details Cards -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <!-- Data Diri Review -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    Identitas Mahasiswa
                </h3>
                <a href="{{ route('student.profile') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Ubah Data</a>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                <div>
                    <dt class="text-slate-500">Nomor Induk Mahasiswa (NIM)</dt>
                    <dd class="font-bold text-slate-900 font-mono">{{ $user->profile->nim }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Nama Lengkap</dt>
                    <dd class="font-bold text-slate-900">{{ $user->profile->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Fakultas / Program Studi</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->profile->faculty }} / {{ $user->profile->major }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">No. WhatsApp / HP</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->profile->phone }}</dd>
                </div>
            </dl>
        </div>

        <!-- Riwayat Kesehatan Review -->
        <div class="space-y-3 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    Ringkasan Kesehatan
                </h3>
                <a href="{{ route('student.health_history') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Ubah Riwayat</a>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs sm:text-sm">
                <div>
                    <dt class="text-slate-500">Gol. Darah & Rhesus</dt>
                    <dd class="font-bold text-teal-700">{{ $user->healthHistory->blood_type }} ({{ $user->healthHistory->rhesus }})</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tinggi / Berat Badan</dt>
                    <dd class="font-semibold text-slate-800">{{ $user->healthHistory->height_cm }} cm / {{ $user->healthHistory->weight_kg }} kg</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Indeks BMI</dt>
                    <dd class="font-bold text-slate-900 font-mono">{{ $user->healthHistory->bmi }} ({{ $user->healthHistory->bmi_status }})</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Riwayat Alergi</dt>
                    <dd class="font-medium text-slate-700">{{ $user->healthHistory->allergies ?: 'Tidak ada' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Riwayat Penyakit</dt>
                    <dd class="font-medium text-slate-700">{{ $user->healthHistory->chronic_diseases ?: 'Tidak ada' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Kontak Darurat</dt>
                    <dd class="font-medium text-slate-700">{{ $user->healthHistory->emergency_contact_name }} ({{ $user->healthHistory->emergency_contact_phone }})</dd>
                </div>
            </dl>
        </div>

        <!-- Jadwal Sesi Review -->
        <div class="space-y-3 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    Sesi Jadwal Skrining
                </h3>
                <a href="{{ route('student.schedule') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Ubah Sesi</a>
            </div>

            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200/60 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-slate-900">
                        {{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>
                    <div class="text-xs font-semibold text-teal-800 mt-0.5">
                        {{ $registration->testSession->session_name }} ({{ substr($registration->testSession->start_time, 0, 5) }} - {{ substr($registration->testSession->end_time, 0, 5) }} WIB)
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Lokasi: Gedung Poliklinik Kampus Induk UINSSC
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white text-teal-700 border border-teal-200">
                        Slot Tersedia
                    </span>
                </div>
            </div>
        </div>

        <!-- Warning Box -->
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            <div>
                <span class="font-bold">Perhatian:</span> Dengan mengklik tombol "Kunci Data & Lanjut Pembayaran", jadwal dan data identitas Anda akan dikunci untuk penerbitan tagihan biaya skrining kesehatan PPKMB.
            </div>
        </div>

        <!-- Lock Action Form -->
        <form method="POST" action="{{ route('student.confirmation.lock') }}" class="pt-4 border-t border-slate-100 flex items-center justify-between">
            @csrf
            <a href="{{ route('student.schedule') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                &larr; Pilih Jadwal Lain
            </a>

            <button type="submit" class="px-7 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                <span>Kunci Data & Lanjut Pembayaran</span>
            </button>
        </form>

    </div>
</div>
@endsection
