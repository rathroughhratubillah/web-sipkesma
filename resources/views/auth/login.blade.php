<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sipkesma</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative"
      style="background-image: url('{{ asset('images/kampus-uinssc.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;"
      x-data="{ activeTab: '{{ request('role') === 'admin' || request('role') === 'staff' ? 'admin' : 'student' }}' }">

    {{-- Overlay gelap transparan agar form tetap terbaca --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>

    <div class="relative z-10 w-full max-w-md space-y-6">
        <!-- Logo -->
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v3m-1.5-1.5h3" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-2xl font-black tracking-tight text-white leading-tight drop-shadow-sm">Sipkesma</div>
                    <div class="text-[10px] font-bold tracking-wider text-teal-300 uppercase">Skrining Kesehatan PPKMB</div>
                </div>
            </a>
            <h2 class="mt-6 text-3xl font-black text-white tracking-tight drop-shadow-md">Masuk ke Portal</h2>
            <p class="mt-1 text-sm text-slate-200 font-medium drop-shadow-xs">Pilih akses sesuai peran Anda di Sistem Sipkesma</p>
        </div>

        <!-- Role Tab Switcher -->
        <div class="p-1.5 bg-slate-900/60 backdrop-blur-md rounded-2xl flex items-center gap-1.5 text-sm font-semibold border border-white/20 shadow-lg">
            <button type="button"
                    @click="activeTab = 'student'"
                    :class="activeTab === 'student' ? 'bg-white text-teal-800 shadow-md font-bold' : 'text-white/80 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                </svg>
                <span>Maba (Mahasiswa Baru)</span>
            </button>
            <button type="button"
                    @click="activeTab = 'admin'"
                    :class="activeTab === 'admin' ? 'bg-white text-teal-800 shadow-md font-bold' : 'text-white/80 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>Admin & Petugas Kampus</span>
            </button>
        </div>

        <!-- Card Form -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-teal-950/5 space-y-5">
            
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    @foreach($errors->all() as $err)
                        <p>{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Google Login Button (Hanya tampil untuk Mahasiswa Baru) -->
            <div x-show="activeTab === 'student'" x-transition class="space-y-2">
                <a href="{{ route('auth.google') }}"
                   class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl border-2 border-teal-500/30 bg-teal-50/50 hover:bg-teal-50 hover:border-teal-500 text-slate-800 font-bold text-sm transition-all shadow-xs group">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span>Masuk dengan Akun Google</span>
                </a>
                <p class="text-center text-[11px] text-slate-500 font-medium">
                    Gunakan akun Google aktif untuk masuk lebih cepat
                </p>

                <!-- Divider -->
                <div class="relative flex items-center gap-3 pt-2 pb-1">
                    <div class="flex-1 h-px bg-slate-100"></div>
                    <span class="text-xs text-slate-400 font-medium">atau masuk dengan email terdaftar</span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5"
                           x-text="activeTab === 'student' ? 'Email Pendaftaran' : 'NIP / Username / Email'">
                        Email
                    </label>
                    <input type="text" name="email" value="{{ old('email') }}" required autofocus
                           :placeholder="activeTab === 'student' ? 'Masukkan email terdaftar' : 'Masukkan NIP atau Email'"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 text-sm outline-hidden transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="rounded text-teal-600 focus:ring-teal-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Tombol Masuk Mahasiswa -->
                <button type="submit"
                        x-show="activeTab === 'student'"
                        class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-sm shadow-md transition bg-teal-600 hover:bg-teal-700 shadow-teal-600/25 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk Portal Maba</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>

                <!-- Tombol Masuk Administrator -->
                <button type="submit"
                        x-show="activeTab === 'admin'"
                        class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-sm shadow-md transition bg-slate-900 hover:bg-slate-800 shadow-slate-900/25 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk Panel Administrator</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </button>
            </form>

            <!-- Footer Card Contextual -->
            <div x-show="activeTab === 'student'" class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
                Belum memiliki akun pendaftaran? 
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700">Daftar Skrining Baru</a>
            </div>

            <div x-show="activeTab === 'admin'" class="pt-3 border-t border-slate-100 text-center text-xs text-slate-400">
                Akses terbatas untuk administrator dan pengelola resmi sistem Sipkesma
            </div>
        </div>

        <div class="text-center text-xs text-slate-200">
            <a href="{{ url('/') }}" class="hover:text-white transition">&larr; Kembali ke Beranda Sipkesma</a>
        </div>
    </div>

</body>
</html>
