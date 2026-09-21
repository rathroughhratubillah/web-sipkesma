@extends('layouts.staff')

@section('title', 'Input Hasil Stasiun Medis')
@section('header_title', $stationInfo['title'])

@section('content')
<div class="space-y-6" x-data="{
    evalModal: false,
    actionUrl: '',
    studentName: '',
    queueCode: '',
    currentStatus: 'pass',
    notes: '{{ $stationInfo['preset_notes'] }}',
    examiner: '{{ $stationInfo['default_examiner'] }}',

    openModal(url, name, code, status, existingNotes, existingExaminer) {
        this.actionUrl = url;
        this.studentName = name;
        this.queueCode = code;
        this.currentStatus = status || 'pass';
        this.notes = existingNotes || '{{ $stationInfo['preset_notes'] }}';
        this.examiner = existingExaminer || '{{ $stationInfo['default_examiner'] }}';
        this.evalModal = true;
    }
}">

    <!-- Station Selection Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-3 rounded-3xl border border-slate-200">
        <div class="flex items-center gap-2">
            <a href="{{ route('staff.station.index', 'urin') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $station_name === 'urin' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🧪 1. Tes Urin Lab
            </a>
            <a href="{{ route('staff.station.index', 'napza') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $station_name === 'napza' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🛡️ 2. Tes Skrining NAPZA
            </a>
            <a href="{{ route('staff.station.index', 'pemeriksaan') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $station_name === 'pemeriksaan' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                🩺 3. Pemeriksaan Fisik & Dokter
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('staff.station.index', $station_name) }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Cari No Antrean / NIM / Nama..."
                   class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:border-teal-500 outline-hidden w-60">
            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('staff.station.index', $station_name) }}" class="text-xs text-rose-500 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Station Info Header Banner -->
    <div class="p-5 rounded-3xl bg-teal-50/70 border border-teal-200/70 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-teal-950">{{ $stationInfo['title'] }}</h2>
            <p class="text-xs text-teal-800 mt-0.5">{{ $stationInfo['desc'] }}</p>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-teal-700">Pemeriksa Default:</span>
            <div class="text-xs font-bold text-slate-800">{{ $stationInfo['default_examiner'] }}</div>
        </div>
    </div>

    <!-- Student Examination Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">No Antrean</th>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Riwayat Singkat</th>
                        <th class="p-4">Hasil Stasiun Ini</th>
                        <th class="p-4">Evaluasi 3 Stasiun</th>
                        <th class="p-4 text-right">Aksi Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($registrations as $reg)
                        @php
                            $u = $reg->user;
                            $prof = $u?->profile;
                            $health = $u?->healthHistory;
                            $thisResult = $reg->getStationResult($station_name);

                            // Station evaluation status for this station
                            $status = $thisResult?->status;
                            $notes = $thisResult?->notes;
                            $examiner = $thisResult?->examiner_name;

                            // 3-station summary
                            $urinRes = $reg->getStationResult('urin');
                            $napzaRes = $reg->getStationResult('napza');
                            $fisikRes = $reg->getStationResult('pemeriksaan');
                        @endphp

                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No Antrean -->
                            <td class="p-4">
                                <span class="font-black font-mono text-teal-900 bg-teal-50 px-2.5 py-1 rounded-md text-sm">
                                    {{ $reg->queue_code ?: '—' }}
                                </span>
                            </td>

                            <!-- Mahasiswa -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $prof?->full_name ?: $u?->name }}</div>
                                <div class="text-slate-500 font-mono">{{ $prof?->nim }} &middot; {{ $prof?->gender === 'L' ? 'L' : 'P' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $prof?->faculty }}</div>
                            </td>

                            <!-- Riwayat Singkat -->
                            <td class="p-4 text-slate-600">
                                @if($health)
                                    <div>Gol: <strong>{{ $health->blood_type }}{{ $health->rhesus }}</strong> | BMI: <strong>{{ $health->bmi }}</strong></div>
                                    <div class="text-[11px] text-slate-400">Alergi: {{ $health->allergies ?: 'Tidak ada' }}</div>
                                @else
                                    <span class="text-slate-400">Belum diisi</span>
                                @endif
                            </td>

                            <!-- Hasil Stasiun Ini -->
                            <td class="p-4">
                                @if($status === 'pass')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Layak (Pass)
                                    </span>
                                @elseif($status === 'followup')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ Tindak Lanjut
                                    </span>
                                @elseif($status === 'fail')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        ✕ Tidak Layak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                        Belum Input
                                    </span>
                                @endif

                                @if($notes)
                                    <div class="text-[11px] text-slate-500 mt-1 max-w-xs truncate" title="{{ $notes }}">{{ $notes }}</div>
                                @endif
                            </td>

                            <!-- Evaluasi 3 Stasiun -->
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $urinRes?->status === 'pass' ? 'bg-emerald-500 text-white' : ($urinRes ? 'bg-amber-400 text-slate-900' : 'bg-slate-200 text-slate-500') }}" title="Urin: {{ $urinRes?->status ?? 'Belum' }}">
                                        U
                                    </span>
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $napzaRes?->status === 'pass' ? 'bg-emerald-500 text-white' : ($napzaRes ? 'bg-amber-400 text-slate-900' : 'bg-slate-200 text-slate-500') }}" title="NAPZA: {{ $napzaRes?->status ?? 'Belum' }}">
                                        N
                                    </span>
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $fisikRes?->status === 'pass' ? 'bg-emerald-500 text-white' : ($fisikRes ? 'bg-amber-400 text-slate-900' : 'bg-slate-200 text-slate-500') }}" title="Fisik: {{ $fisikRes?->status ?? 'Belum' }}">
                                        F
                                    </span>
                                    @if($reg->status === 'completed')
                                        <span class="text-[11px] text-emerald-700 font-bold ml-1">Lulus</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="p-4 text-right">
                                <button type="button" 
                                        @click="openModal('{{ route('staff.station.store', [$station_name, $reg]) }}', '{{ addslashes($prof?->full_name ?: $u?->name) }}', '{{ $reg->queue_code }}', '{{ $status }}', '{{ addslashes($notes ?? '') }}', '{{ addslashes($examiner ?? '') }}')"
                                        class="px-4 py-2 rounded-xl {{ $status ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-teal-600 hover:bg-teal-700 text-white shadow-xs' }} font-bold text-xs transition">
                                    {{ $status ? 'Edit Hasil' : 'Input Hasil' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tidak ada peserta dalam antrean stasiun ini. Pastikan peserta telah melakukan check-in di meja depan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Input / Edit Hasil Stasiun -->
    <div x-show="evalModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-100" @click.outside="evalModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Input Hasil: {{ $stationInfo['title'] }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Peserta: <strong class="text-slate-900" x-text="studentName"></strong> (<span class="font-mono text-teal-700 font-bold" x-text="queueCode"></span>)
                    </p>
                </div>
                <button @click="evalModal = false" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form :action="actionUrl" method="POST" class="space-y-4">
                @csrf

                <!-- Status Evaluation Radios -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Hasil Evaluasi Medis <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="p-3 rounded-2xl border-2 cursor-pointer text-center transition"
                               :class="currentStatus === 'pass' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status" value="pass" x-model="currentStatus" class="sr-only">
                            <span class="block text-xs font-bold">✓ Layak (Pass)</span>
                        </label>

                        <label class="p-3 rounded-2xl border-2 cursor-pointer text-center transition"
                               :class="currentStatus === 'followup' ? 'border-amber-500 bg-amber-50 text-amber-800' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status" value="followup" x-model="currentStatus" class="sr-only">
                            <span class="block text-xs font-bold">⏳ Tindak Lanjut</span>
                        </label>

                        <label class="p-3 rounded-2xl border-2 cursor-pointer text-center transition"
                               :class="currentStatus === 'fail' ? 'border-rose-500 bg-rose-50 text-rose-800' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="status" value="fail" x-model="currentStatus" class="sr-only">
                            <span class="block text-xs font-bold">✕ Tidak Layak</span>
                        </label>
                    </div>
                </div>

                <!-- Doctor Examiner Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Dokter / Petugas Pemeriksa <span class="text-rose-500">*</span></label>
                    <input type="text" name="examiner_name" x-model="examiner" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-semibold outline-hidden">
                </div>

                <!-- Notes / Findings -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Catatan / Keterangan Medis</label>
                        <button type="button" 
                                @click="notes = '{{ $stationInfo['preset_notes'] }}'" 
                                class="text-[10px] text-teal-600 hover:underline">
                            Gunakan Catatan Normal
                        </button>
                    </div>
                    <textarea name="notes" x-model="notes" rows="3"
                              class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden"></textarea>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 border border-slate-200/60">
                    💡 <em>Jika seluruh 3 stasiun (Urin, NAPZA, dan Fisik) telah berstatus "Pass", sistem secara otomatis mengubah status mahasiswa menjadi "COMPLETED" dan sertifikat resmi langsung dapat diunduh.</em>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="evalModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20">
                        Simpan Hasil Medis
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
