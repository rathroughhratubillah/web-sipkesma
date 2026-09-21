<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Skrining — Sipkesma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6fbfa; }</style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-md space-y-6">
        <!-- Logo -->
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v3m-1.5-1.5h3" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-2xl font-bold tracking-tight text-slate-900 leading-tight">Sipkesma</div>
                    <div class="text-[10px] font-semibold tracking-wider text-slate-400 uppercase">Skrining Kesehatan PPKMB</div>
                </div>
            </a>
            <h2 class="mt-6 text-2xl font-bold text-slate-900">Registrasi Mahasiswa Baru</h2>
            <p class="mt-1 text-sm text-slate-500">Buat akun untuk memulai tahapan pemeriksaan PPKMB</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-teal-950/5 space-y-5">
            
            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Nama sesuai ijazah / KTP"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Aktif</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="nama@email.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Ulangi kata sandi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/25 transition">
                    Daftar & Lanjutkan
                </button>
            </form>

            <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700">Masuk di sini</a>
            </div>
        </div>
    </div>

</body>
</html>
