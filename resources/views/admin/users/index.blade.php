@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('header_title', 'Kelola Pengguna')
@section('header_subtitle', 'Daftar semua akun pengguna yang terdaftar di sistem')

@section('content')
<div class="space-y-4">

    <!-- Filter & Search -->
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Pengguna</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Nama atau email..."
                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Role</label>
            <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
                <option value="all">Semua Role</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ $role === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
            🔍 Cari
        </button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Daftar Pengguna</h2>
                <p class="text-xs text-slate-400">Total: {{ $users->total() }} pengguna</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-xs font-semibold hover:bg-violet-700 transition flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Tambah Pengguna</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Status Data</th>
                        <th class="px-5 py-3">Terdaftar</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3 text-slate-400">{{ $user->id }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-600">{{ $user->profile?->nim ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @foreach($user->roles as $r)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $r->name === 'admin' ? 'bg-violet-100 text-violet-700' : 
                                       ($r->name === 'staff' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                                    {{ $r->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $user->profile ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $user->profile ? '✓ Profil' : '✗ Profil' }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $user->registration ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $user->registration ? '✓ Reg' : '✗ Reg' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 font-semibold hover:bg-violet-100 transition text-[11px]">
                                    ✏️ Edit
                                </a>
                                @if($user->id !== Auth::id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Yakin hapus pengguna ini? Semua datanya akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition text-[11px]">
                                        🗑️ Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
