@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')
@section('header_title', 'Tambah Pengguna Baru')
@section('header_subtitle', 'Buat akun pengguna baru langsung ke database')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-violet-600 transition font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Kembali ke Daftar Pengguna
    </a>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-bold text-slate-800">Formulir Tambah Pengguna</h2>
            <p class="text-xs text-slate-500 mt-0.5">Isi data akun pengguna yang ingin didaftarkan ke sistem.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e)
                    <div>• {{ $e }}</div>
                @endforeach
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Contoh: Muhammad Ilham"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="nama@uinssc.ac.id"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Peran / Role <span class="text-red-500">*</span></label>
                <select name="role" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent">
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : ($r->name === 'student' ? 'selected' : '') }}>
                            {{ ucfirst($r->name) }} {{ $r->name === 'student' ? '(Mahasiswa)' : ($r->name === 'staff' ? '(Petugas Medis)' : '(Administrator)') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                    ✨ Buat Akun Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
