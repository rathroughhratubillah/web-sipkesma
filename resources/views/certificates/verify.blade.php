<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat — Sipkesma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6fbfa; }</style>
</head>
<body class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">

    <div class="max-w-xl w-full space-y-6">
        <!-- Logo -->
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-xl font-bold text-slate-900 leading-tight">Sipkesma</div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase">Verifikasi Dokumen Resmi</div>
                </div>
            </a>
        </div>

        @if($registration)
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl space-y-6 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        DOKUMEN VALID & RESMI
                    </span>
                    <h1 class="text-xl font-bold text-slate-900 mt-2">Surat Keterangan Kesehatan Terdaftar</h1>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $code }}</p>
                </div>

                <div class="text-left p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nama Lengkap</span>
                        <span class="font-bold text-slate-900">{{ $registration->user->profile->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIM</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $registration->user->profile->nim }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Fakultas / Prodi</span>
                        <span class="font-semibold text-slate-800">{{ $registration->user->profile->faculty }} / {{ $registration->user->profile->major }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Kelulusan</span>
                        <span class="font-bold text-emerald-700">LULUS / SEHAT (PASS)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu Pemeriksaan</span>
                        <span class="text-slate-700 font-medium">{{ $registration->completed_at?->format('d/m/Y H:i') }} WIB</span>
                    </div>
                </div>

                <div class="text-xs text-slate-400">
                    Dokumen ini telah terdaftar secara sah pada basis data Klinik Sipkesma PPKMB UINSSC.
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl space-y-4 text-center">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                </div>
                <h1 class="text-xl font-bold text-slate-900">Dokumen Tidak Ditemukan</h1>
                <p class="text-xs text-slate-500">
                    Nomor sertifikat tidak terdaftar atau belum disahkan oleh tim dokter pemeriksa.
                </p>
                <a href="{{ url('/') }}" class="inline-block px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold mt-2">
                    Kembali ke Beranda
                </a>
            </div>
        @endif

    </div>

</body>
</html>
