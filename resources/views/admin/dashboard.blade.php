@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Admin')
@section('header_subtitle', 'Ringkasan data dan progres skrining PPKMB')

@section('content')
<div class="space-y-5">

    {{-- 0. FILTER TAHUN AKADEMIK / ANGKATAN --}}
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base shrink-0">
                🗓️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900">Filter Tahun Akademik PPKMB</h3>
                    @if(($selectedYear ?? 'all') !== 'all')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                            Tahun {{ $selectedYear }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                            Semua Tahun
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500">Melihat data lampau dan statistik skrining mahasiswa per tahun angkatan</p>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 shrink-0">
            <label for="year-select" class="text-xs font-semibold text-slate-600">Pilih Tahun:</label>
            <select id="year-select" name="year" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                <option value="all" {{ ($selectedYear ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tahun (Total Rekap)</option>
                @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ (string)($selectedYear ?? '') === (string)$yr ? 'selected' : '' }}>
                        Tahun {{ $yr }} {{ $yr == date('Y') ? '(Tahun Aktif)' : '(Data Lampau)' }}
                    </option>
                @endforeach
            </select>
            @if(($selectedYear ?? 'all') !== 'all')
                <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Tampilkan Semua Tahun">
                    ✕ Reset
                </a>
            @endif
        </form>
    </div>

    {{-- 1. STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-teal-300 transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base shrink-0">👤</div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['total_users'] }}</div>
                <div class="text-xs text-slate-400 font-medium">Pengguna</div>
            </div>
        </a>

        <a href="{{ route('admin.profiles.index', ['year' => $selectedYear]) }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-blue-300 transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">🎓</div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['total_profiles'] }}</div>
                <div class="text-xs text-slate-400 font-medium">Profil Mahasiswa</div>
            </div>
        </a>

        <a href="{{ route('admin.health.index', ['year' => $selectedYear]) }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:shadow-md hover:border-rose-300 transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-base shrink-0">🩺</div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['total_health'] }}</div>
                <div class="text-xs text-slate-400 font-medium">Riwayat Kesehatan</div>
            </div>
        </a>

        <div class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0">📜</div>
            <div>
                <div class="text-2xl font-black text-emerald-700 leading-tight">{{ $stats['completed'] }}</div>
                <div class="text-xs text-slate-400 font-medium">Lulus Skrining</div>
            </div>
        </div>
    </div>

    {{-- 2. DAFTAR CEPAT + GRAFIK (side by side on large screens) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- 2a. Arahan Rekap (compact) --}}
        <div class="bg-slate-900 rounded-2xl p-5 space-y-4 border border-slate-800">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-400">Panduan Rekap Data</span>
                <h2 class="text-sm font-black text-white mt-1 leading-snug">
                    Lihat data mahasiswa per fakultas &amp; jurusan
                </h2>
                <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed">
                    Pilih fakultas → pilih jurusan → buka tabel mahasiswa. Tersedia ekspor CSV per jurusan.
                </p>
            </div>

            <div class="space-y-2">
                <a href="{{ route('staff.participants.index', ['year' => $selectedYear]) }}"
                   class="flex items-center justify-between w-full px-3.5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-white font-bold text-xs transition">
                    <span>📋 Rekap Peserta PPKMB</span>
                    <span>&rarr;</span>
                </a>
                <a href="{{ route('admin.health.index', ['year' => $selectedYear]) }}"
                   class="flex items-center justify-between w-full px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition">
                    <span>🩺 Rekap Riwayat Kesehatan</span>
                    <span>&rarr;</span>
                </a>
            </div>

            {{-- Shortcut Fakultas --}}
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Akses Cepat Fakultas</span>
                <div class="space-y-1.5">
                    @php
                        $facShort = [
                            'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)' => 'FITK',
                            'Fakultas Ekonomi Dan Bisnis Islam (FEBI)'   => 'FEBI',
                            'Fakultas Syariah'                            => 'Syariah',
                            'Fakultas Dakwah Dan Komunikasi Islam (FDKI)' => 'FDKI',
                            'Fakultas Ushuluddin Dan Arab'                => 'Ushuluddin',
                        ];
                    @endphp
                    @foreach($facShort as $facKey => $facLabel)
                        @php $cnt = $facultyCounts[$facKey] ?? 0; @endphp
                        <a href="{{ route('staff.participants.index', ['faculty' => $facKey, 'year' => $selectedYear]) }}"
                           class="flex items-center justify-between px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-teal-900/60 border border-slate-700/60 hover:border-teal-500/50 text-xs text-slate-300 hover:text-white font-medium transition">
                            <span>{{ $facLabel }}</span>
                            <span class="font-mono font-bold text-teal-400 text-[10px]">{{ $cnt }} Mhs</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 2b. Grafik (area chart compact) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                        <h2 class="text-sm font-black text-slate-900">Tren Progres (7 Hari Terakhir)</h2>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pendaftaran, pembayaran, dan penyelesaian skrining</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap text-[11px] font-bold">
                    <span class="flex items-center gap-1 text-teal-700"><span class="w-2 h-2 rounded-full bg-teal-500 inline-block"></span> Daftar</span>
                    <span class="flex items-center gap-1 text-blue-700"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span> Bayar</span>
                    <span class="flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Selesai</span>
                </div>
            </div>

            <div class="relative h-52">
                <canvas id="progressTrendChart" class="w-full h-full"></canvas>
            </div>

            {{-- Status Funnel strip --}}
            <div class="pt-3 border-t border-slate-100 grid grid-cols-7 gap-1.5 text-center">
                @php
                $steps = [
                    ['label' => 'Draft',     'color' => 'slate',   'val' => $statusCounts['draft']],
                    ['label' => 'Tunggu\nBayar', 'color' => 'amber', 'val' => $statusCounts['awaiting_payment']],
                    ['label' => 'Tunggu\nVerif', 'color' => 'blue',  'val' => $statusCounts['awaiting_verification']],
                    ['label' => 'Tiket',     'color' => 'cyan',    'val' => $statusCounts['cleared']],
                    ['label' => 'Hadir',     'color' => 'indigo',  'val' => $statusCounts['checked_in']],
                    ['label' => 'Diperiksa', 'color' => 'purple',  'val' => $statusCounts['in_progress']],
                    ['label' => 'Selesai',   'color' => 'emerald', 'val' => $statusCounts['completed']],
                ];
                $colorMap = [
                    'slate'   => 'bg-slate-100 text-slate-600',
                    'amber'   => 'bg-amber-100 text-amber-700',
                    'blue'    => 'bg-blue-100 text-blue-700',
                    'cyan'    => 'bg-cyan-100 text-cyan-700',
                    'indigo'  => 'bg-indigo-100 text-indigo-700',
                    'purple'  => 'bg-purple-100 text-purple-700',
                    'emerald' => 'bg-emerald-100 text-emerald-700',
                ];
                @endphp
                @foreach($steps as $step)
                <div class="rounded-xl p-2 {{ $colorMap[$step['color']] }}">
                    <div class="text-base font-black leading-none">{{ $step['val'] }}</div>
                    <div class="text-[9px] font-semibold mt-1 leading-tight">{{ str_replace('\n', '<br>', $step['label']) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. RECENT USERS TABLE (compact) --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Pengguna Terbaru</h2>
            <div class="flex items-center gap-3 text-xs font-semibold">
                <a href="{{ route('staff.participants.index', ['year' => $selectedYear]) }}" class="text-teal-600 hover:underline">Rekap Peserta &rarr;</a>
                <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-700 transition">Lihat Semua &rarr;</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Fakultas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUsers as $u)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-4 py-2.5">
                            <div class="font-semibold text-slate-800 truncate max-w-[140px]">{{ $u->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate max-w-[140px]">{{ $u->email }}</div>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-slate-600 text-[11px]">{{ $u->profile?->nim ?? '—' }}</td>
                        <td class="px-4 py-2.5 hidden sm:table-cell text-slate-500 max-w-[180px] truncate text-[11px]">{{ $u->profile?->faculty ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            @if($u->registration)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ match($u->registration->status) {
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'draft'     => 'bg-slate-100 text-slate-500',
                                        default     => 'bg-amber-100 text-amber-700'
                                    } }}">
                                    {{ $u->registration->status }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 text-[11px] font-semibold hover:bg-teal-100 transition">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('progressTrendChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const ctx = canvas.getContext('2d');

    const gReg = ctx.createLinearGradient(0, 0, 0, 200);
    gReg.addColorStop(0, 'rgba(13,148,136,0.28)');
    gReg.addColorStop(1, 'rgba(13,148,136,0)');

    const gPay = ctx.createLinearGradient(0, 0, 0, 200);
    gPay.addColorStop(0, 'rgba(59,130,246,0.18)');
    gPay.addColorStop(1, 'rgba(59,130,246,0)');

    const gComp = ctx.createLinearGradient(0, 0, 0, 200);
    gComp.addColorStop(0, 'rgba(16,185,129,0.22)');
    gComp.addColorStop(1, 'rgba(16,185,129,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartDates),
            datasets: [
                {
                    label: 'Pendaftaran',
                    data: @json($chartRegistrations),
                    borderColor: '#0d9488',
                    backgroundColor: gReg,
                    fill: true, tension: 0.4, borderWidth: 2.5,
                    pointBackgroundColor: '#0d9488', pointBorderColor: '#fff',
                    pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6,
                },
                {
                    label: 'Bayar Diverifikasi',
                    data: @json($chartPayments),
                    borderColor: '#3b82f6',
                    backgroundColor: gPay,
                    fill: true, tension: 0.4, borderWidth: 2,
                    pointBackgroundColor: '#3b82f6', pointBorderColor: '#fff',
                    pointBorderWidth: 2, pointRadius: 3.5, pointHoverRadius: 5.5,
                },
                {
                    label: 'Selesai Skrining',
                    data: @json($chartCompleted),
                    borderColor: '#10b981',
                    backgroundColor: gComp,
                    fill: true, tension: 0.4, borderWidth: 2,
                    pointBackgroundColor: '#10b981', pointBorderColor: '#fff',
                    pointBorderWidth: 2, pointRadius: 3.5, pointHoverRadius: 5.5,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    titleFont: { size: 11, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 10, cornerRadius: 10, boxPadding: 4,
                    usePointStyle: true,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' }, color: '#94a3b8' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { precision: 0, font: { size: 10 }, color: '#cbd5e1' }
                }
            }
        }
    });
});
</script>
@endsection
