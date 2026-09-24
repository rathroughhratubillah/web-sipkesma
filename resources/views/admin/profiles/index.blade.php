@extends('layouts.admin')

@section('title', 'Profil Mahasiswa')
@section('header_title', 'Kelola Profil Mahasiswa')
@section('header_subtitle', 'Semua data profil yang terisi oleh mahasiswa')

@section('content')
<div class="space-y-4">

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Nama, NISN, atau telepon..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <div class="min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Fakultas</label>
            <select name="faculty" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Fakultas</option>
                @foreach($faculties as $f)
                    <option value="{{ $f }}" {{ $faculty === $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">🔍 Cari</button>
        <a href="{{ route('admin.profiles.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Profil Mahasiswa</h2>
            <p class="text-xs text-slate-400">Total: {{ $profiles->total() }} data profil</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Fakultas / Prodi</th>
                        <th class="px-5 py-3">Jenis Kelamin</th>
                        <th class="px-5 py-3">No. HP</th>
                        <th class="px-5 py-3">Tanggal Lahir</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($profiles as $profile)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $profile->full_name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $profile->user?->email }}</div>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-600">{{ $profile->nim }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-slate-700 max-w-[180px]">{{ $profile->faculty }}</div>
                            <div class="text-[11px] text-slate-400">{{ $profile->major }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $profile->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                {{ $profile->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $profile->phone }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $profile->birth_date?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.profiles.edit', $profile) }}"
                                   class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.profiles.destroy', $profile) }}"
                                      onsubmit="return confirm('Yakin hapus profil ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">Tidak ada data profil ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($profiles->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $profiles->links() }}</div>
        @endif
    </div>

</div>
@endsection
