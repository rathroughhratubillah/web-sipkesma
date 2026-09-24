@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Admin')
@section('header_subtitle', 'Ringkasan semua data yang tersimpan di database')

@section('content')
<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</span>
                <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $stats['total_users'] }}</div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-violet-600 font-semibold mt-1 block hover:underline">Kelola Pengguna →</a>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Profil Terisi</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $stats['total_profiles'] }}</div>
            <a href="{{ route('admin.profiles.index') }}" class="text-xs text-blue-600 font-semibold mt-1 block hover:underline">Lihat Profil →</a>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Data Kesehatan</span>
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $stats['total_health'] }}</div>
            <a href="{{ route('admin.health.index') }}" class="text-xs text-rose-600 font-semibold mt-1 block hover:underline">Lihat Data →</a>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Skrining</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ $stats['completed'] }}</div>
            <span class="text-xs text-emerald-600 font-semibold mt-1 block">Sertifikat Terbit</span>
        </div>

    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        @php
        $quickLinks = [
            ['label' => 'Pengguna', 'icon' => '👤', 'route' => 'admin.users.index', 'count' => $stats['total_users'], 'color' => 'violet'],
            ['label' => 'Profil', 'icon' => '🎓', 'route' => 'admin.profiles.index', 'count' => $stats['total_profiles'], 'color' => 'blue'],
            ['label' => 'Kesehatan', 'icon' => '🏥', 'route' => 'admin.health.index', 'count' => $stats['total_health'], 'color' => 'rose'],
            ['label' => 'Registrasi', 'icon' => '📋', 'route' => 'admin.registrations.index', 'count' => $stats['total_registrations'], 'color' => 'amber'],
            ['label' => 'Pembayaran', 'icon' => '💳', 'route' => 'admin.payments.index', 'count' => $stats['total_payments'], 'color' => 'emerald'],
            ['label' => 'Hasil Skrining', 'icon' => '🔬', 'route' => 'admin.results.index', 'count' => $stats['total_results'], 'color' => 'cyan'],
        ];
        @endphp

        @foreach($quickLinks as $link)
        <a href="{{ route($link['route']) }}" 
           class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group flex items-center gap-3">
            <div class="text-2xl">{{ $link['icon'] }}</div>
            <div>
                <div class="text-xs text-slate-500 font-medium">{{ $link['label'] }}</div>
                <div class="text-xl font-black text-slate-900">{{ $link['count'] }}</div>
            </div>
            <svg class="w-4 h-4 text-slate-400 ml-auto group-hover:text-violet-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </a>
        @endforeach
    </div>

    <!-- Recent Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Pengguna Terbaru yang Daftar</h2>
                <p class="text-xs text-slate-400 mt-0.5">Semua data tersimpan otomatis di database</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-violet-600 hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Fakultas</th>
                        <th class="px-5 py-3">Status Registrasi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUsers as $user)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-600">{{ $user->profile?->nim ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 max-w-[200px] truncate">{{ $user->profile?->faculty ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($user->registration)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $user->registration->status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                                       ($user->registration->status === 'draft' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $user->registration->status }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="px-3 py-1 rounded-lg bg-violet-50 text-violet-700 text-[11px] font-semibold hover:bg-violet-100 transition">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada pengguna terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
