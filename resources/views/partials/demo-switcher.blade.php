{{-- Floating Quick Demo Switcher Widget --}}
<div x-data="{
        open: false,
        minimized: false,
        hidden: sessionStorage.getItem('sipkesma_demo_hidden') === 'true'
     }"
     x-show="!hidden"
     x-cloak
     class="fixed bottom-5 right-5 z-50 font-sans print:hidden">

    <!-- Popover Card when opened -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-3 scale-95"
         @click.outside="open = false"
         class="mb-3 w-80 sm:w-96 rounded-2xl bg-slate-900/95 backdrop-blur-lg border border-slate-700/80 shadow-2xl text-slate-200 overflow-hidden text-xs">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-slate-800/80 border-b border-slate-700/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                <span class="font-bold text-white tracking-wide">Panel Pengujian Akun Demo</span>
            </div>
            <div class="flex items-center gap-1">
                <button type="button"
                        @click="hidden = true; sessionStorage.setItem('sipkesma_demo_hidden', 'true')"
                        title="Sembunyikan widget ini untuk presentasi"
                        class="p-1 text-slate-400 hover:text-slate-200 hover:bg-slate-700 rounded-lg transition"
                        aria-label="Sembunyikan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
                <button type="button"
                        @click="open = false"
                        class="p-1 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition"
                        aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Current Session Info -->
        @auth
        <div class="px-4 py-2.5 bg-teal-950/40 border-b border-teal-800/40 flex items-center justify-between gap-2 text-[11px]">
            <div class="truncate">
                <span class="text-slate-400">Aktif:</span>
                <span class="font-semibold text-teal-300">{{ Auth::user()->name }}</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] bg-teal-500/20 text-teal-300 font-mono ml-1 uppercase">
                    {{ Auth::user()->roles->first()?->name ?? 'User' }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="text-rose-400 hover:text-rose-300 font-medium underline">
                    Logout
                </button>
            </form>
        </div>
        @endauth

        <div class="p-3 max-h-80 overflow-y-auto space-y-3">
            <!-- Mahasiswa Section -->
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-teal-400 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                    </svg>
                    <span>Peran Mahasiswa</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('demo.login', 'ratu') }}"
                       class="flex items-center justify-between p-2 rounded-xl bg-slate-800 hover:bg-teal-900/50 hover:border-teal-500/50 border border-slate-700/60 transition group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-[10px]">
                                1
                            </div>
                            <div>
                                <div class="font-medium text-slate-100 group-hover:text-teal-200">Ratu (Mahasiswa Baru)</div>
                                <div class="text-[10px] text-slate-400">Status: Form Draft / Isi Berkas</div>
                            </div>
                        </div>
                        <span class="text-teal-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>

                    <a href="{{ route('demo.login', 'budi') }}"
                       class="flex items-center justify-between p-2 rounded-xl bg-slate-800 hover:bg-teal-900/50 hover:border-teal-500/50 border border-slate-700/60 transition group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-[10px]">
                                2
                            </div>
                            <div>
                                <div class="font-medium text-slate-100 group-hover:text-teal-200">Budi (Bertiket Antrean)</div>
                                <div class="text-[10px] text-slate-400">Status: Sudah Bayar, Punya QR Tiket</div>
                            </div>
                        </div>
                        <span class="text-teal-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>

                    <a href="{{ route('demo.login', 'siti') }}"
                       class="flex items-center justify-between p-2 rounded-xl bg-slate-800 hover:bg-teal-900/50 hover:border-teal-500/50 border border-slate-700/60 transition group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-[10px]">
                                3
                            </div>
                            <div>
                                <div class="font-medium text-slate-100 group-hover:text-emerald-200">Siti (Selesai Skrining)</div>
                                <div class="text-[10px] text-slate-400">Status: Lulus & Sertifikat Siap Unduh</div>
                            </div>
                        </div>
                        <span class="text-emerald-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Petugas & Admin Section -->
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-purple-400 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Peran Staf & Administrator</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('demo.login', 'staff') }}"
                       class="flex items-center justify-between p-2 rounded-xl bg-slate-800 hover:bg-amber-900/40 hover:border-amber-500/50 border border-slate-700/60 transition group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center font-bold text-[10px]">
                                🩺
                            </div>
                            <div>
                                <div class="font-medium text-slate-100 group-hover:text-amber-200">dr. Nurul (Petugas Medis)</div>
                                <div class="text-[10px] text-slate-400">Verifikasi, Scanner & Stasiun Tes</div>
                            </div>
                        </div>
                        <span class="text-amber-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>

                    <a href="{{ route('demo.login', 'admin') }}"
                       class="flex items-center justify-between p-2 rounded-xl bg-slate-800 hover:bg-purple-900/40 hover:border-purple-500/50 border border-slate-700/60 transition group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center font-bold text-[10px]">
                                🛡️
                            </div>
                            <div>
                                <div class="font-medium text-slate-100 group-hover:text-purple-200">Super Admin (Administrator)</div>
                                <div class="text-[10px] text-slate-400">Login: <span class="text-purple-300 font-mono">admin</span> | Sandi: <span class="text-purple-300 font-mono font-semibold">admin123</span></div>
                            </div>
                        </div>
                        <span class="text-purple-400 group-hover:translate-x-0.5 transition">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Help -->
        <div class="px-4 py-2 bg-slate-950 border-t border-slate-800 flex items-center justify-between text-[10px] text-slate-400">
            <span>Klik profil untuk auto-login instan</span>
            <button type="button"
                    @click="hidden = true; sessionStorage.setItem('sipkesma_demo_hidden', 'true')"
                    class="text-teal-400 hover:underline">
                Sembunyikan
            </button>
        </div>
    </div>

    <!-- Trigger Button (Floating Pill) -->
    <div class="flex items-center gap-2 justify-end">
        <button type="button"
                @click="open = !open"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-slate-900/95 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-700/80 shadow-xl shadow-black/30 backdrop-blur-md transition-all hover:scale-105 active:scale-95 focus:outline-hidden">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
            </span>
            <span class="text-xs font-semibold tracking-tight">Akun Uji Coba</span>
            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>
    </div>
</div>

<!-- Floating Tiny Re-open Button if hidden -->
<div x-data="{
        get hidden() { return sessionStorage.getItem('sipkesma_demo_hidden') === 'true' }
     }"
     x-show="hidden"
     x-cloak
     class="fixed bottom-3 right-3 z-40 print:hidden font-sans">
    <button type="button"
            @click="sessionStorage.removeItem('sipkesma_demo_hidden'); window.location.reload()"
            title="Tampilkan kembali Panel Akun Uji Coba"
            class="w-7 h-7 rounded-full bg-slate-900/70 hover:bg-slate-900 text-slate-400 hover:text-teal-300 border border-slate-700/50 shadow-md flex items-center justify-center text-[10px] transition">
        ⚙️
    </button>
</div>
