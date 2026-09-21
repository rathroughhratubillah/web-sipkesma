@extends('layouts.student')

@section('title', 'Sertifikat Kesehatan PPKMB')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sertifikat Hasil Skrining Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-1">Dokumen resmi kelulusan skrining kesehatan untuk syarat registrasi PPKMB.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200">
            Langkah 11 dari 11
        </span>
    </div>

    @if($isEligible)
        <!-- Certificate Preview & Download Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-100 shadow-xl space-y-8">
            
            <div class="text-center space-y-3 pb-6 border-b border-slate-100">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto shadow-md shadow-emerald-500/10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    LULUS SKRINING KESEHATAN (FIT FOR STUDY)
                </span>
                <h2 class="text-2xl font-extrabold text-slate-900">Surat Keterangan Kesehatan PPKMB</h2>
                <p class="text-xs text-slate-500 font-mono">Nomor: SIPKESMA/SKK/{{ date('Y') }}/{{ str_pad($registration->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>

            <!-- Identity Review in Certificate -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm bg-slate-50 p-5 rounded-2xl border border-slate-200/60">
                <div>
                    <span class="text-slate-400 block font-medium">Nama Mahasiswa</span>
                    <span class="font-bold text-slate-900 text-base">{{ $user->profile->full_name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">NIM</span>
                    <span class="font-bold text-slate-900 font-mono text-base">{{ $user->profile->nim }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Fakultas / Program Studi</span>
                    <span class="font-semibold text-slate-800">{{ $user->profile->faculty }} &middot; {{ $user->profile->major }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Golongan Darah / BMI</span>
                    <span class="font-semibold text-slate-800">{{ $user->healthHistory->blood_type }} ({{ $user->healthHistory->rhesus }}) &middot; BMI {{ $user->healthHistory->bmi }} ({{ $user->healthHistory->bmi_status }})</span>
                </div>
            </div>

            <!-- Medical Test Summary Table -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Hasil Pemeriksaan 3 Stasiun Medis</h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">Stasiun Pemeriksaan</th>
                                <th class="p-3">Hasil Evaluasi</th>
                                <th class="p-3">Catatan Medis</th>
                                <th class="p-3">Dokter Pemeriksa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach(['urin' => 'Tes Urin Lengkap', 'napza' => 'Skrining Bebas NAPZA (6 Parameter)', 'pemeriksaan' => 'Pemeriksaan Fisik & TTV'] as $stKey => $stLabel)
                                @php $res = $registration->getStationResult($stKey); @endphp
                                <tr>
                                    <td class="p-3 font-semibold text-slate-900">{{ $stLabel }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            LAYAK (PASS)
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600">{{ $res?->notes ?: 'Dalam batas normal' }}</td>
                                    <td class="p-3 text-slate-700">{{ $res?->examiner_name ?: 'Tim Medis' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Download Action -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
                <div class="text-xs text-slate-500">
                    Dokumen dilengkapi QR Code tanda tangan digital resmi dan dapat divalidasi keasliannya secara online.
                </div>
                <a href="{{ route('student.certificate.download') }}" 
                   class="px-8 py-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-lg shadow-teal-600/25 transition flex items-center gap-2 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Unduh Sertifikat PDF Resmi</span>
                </a>
            </div>

        </div>
    @else
        <!-- Locked State -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 text-center space-y-4 shadow-sm">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Sertifikat Belum Tersedia</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                Sertifikat kesehatan resmi hanya dapat diunduh setelah Anda menyelesaikan check-in dan seluruh 3 stasiun medis (Tes Urin, Tes NAPZA, dan Pemeriksaan Fisik) dinyatakan lulus (<strong class="text-emerald-600">PASS</strong>) oleh dokter pemeriksa.
            </p>
            <div class="pt-2">
                <a href="{{ route('student.status') }}" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/20 transition inline-block">
                    Cek Status Pemeriksaan &rarr;
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
