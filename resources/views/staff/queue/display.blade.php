@extends('layouts.staff')

@section('title', 'Layar Panggilan Antrean')
@section('header_title', 'Layar Display Panggilan Antrean')

@section('content')
<div class="space-y-6" x-data="{
    currentNumber: '{{ $currentCalling?->queue_code ?: ($waitingList->first()?->queue_code ?: '-') }}',
    currentName: '{{ addslashes($currentCalling?->user?->name ?: ($waitingList->first()?->user?->name ?: 'Belum Ada')) }}',
    destination: 'Ruang Pemeriksaan Medis',

    speakCall(number, name, dest) {
        if (!('speechSynthesis' in window)) {
            alert('Browser tidak mendukung audio speech');
            return;
        }
        window.speechSynthesis.cancel();
        
        let text = 'Nomor antrean... ' + number + '... ' + name + '... silakan menuju ' + dest;
        let utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = 0.9;
        utter.pitch = 1.0;
        window.speechSynthesis.speak(utter);
    },

    async callQueue(regId, queueNum, studentName) {
        this.currentNumber = queueNum;
        this.currentName = studentName;

        try {
            await fetch('/petugas/antrian/' + regId + '/call', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ destination: this.destination })
            });
        } catch (e) {
            console.error('Call status update error', e);
        }

        this.speakCall(queueNum, studentName, this.destination);
    }
}">

    <!-- Top Session Selector -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-slate-200">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sesi Skrining:</span>
            <form method="GET" action="{{ route('staff.queue.display') }}" class="inline">
                <select name="session_id" onchange="this.form.submit()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-teal-500 outline-hidden">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}" {{ $selectedSessionId == $s->id ? 'selected' : '' }}>
                            {{ $s->session_name }} ({{ \Carbon\Carbon::parse($s->session_date)->locale('id')->isoFormat('D MMM Y') }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500">Tujuan:</span>
            <select x-model="destination" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold bg-white">
                <option value="Meja Check-in">Meja Check-in</option>
                <option value="Stasiun 1: Tes Urin">Stasiun 1: Tes Urin</option>
                <option value="Stasiun 2: Tes NAPZA">Stasiun 2: Tes NAPZA</option>
                <option value="Stasiun 3: Pemeriksaan Fisik">Stasiun 3: Pemeriksaan Fisik</option>
                <option value="Ruang Tunggu Medis">Ruang Tunggu Medis</option>
            </select>
        </div>
    </div>

    <!-- Main Display Stage (Auditorium / Clinic Display Board) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Big Active Calling Monitor (7 cols) -->
        <div class="lg:col-span-7 bg-gradient-to-b from-slate-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 sm:p-12 shadow-2xl border-4 border-slate-800 text-center space-y-6">
            
            <div class="flex items-center justify-between text-xs text-teal-300 font-semibold border-b border-slate-800 pb-4">
                <span>KLINIK UTAMA PPKMB UINSSC</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    LIVE MONITOR
                </span>
            </div>

            <!-- Huge Queue Number -->
            <div class="space-y-2 py-4">
                <span class="text-xs font-bold text-teal-400 tracking-widest uppercase">Nomor Antrean Sedang Dipanggil</span>
                <div class="text-7xl sm:text-9xl font-black font-mono tracking-wider text-teal-300 drop-shadow-md" x-text="currentNumber">
                    {{ $currentCalling?->queue_code ?: ($waitingList->first()?->queue_code ?: '-') }}
                </div>
                <div class="text-xl sm:text-2xl font-bold text-white tracking-wide" x-text="currentName">
                    {{ $currentCalling?->user?->name ?: ($waitingList->first()?->user?->name ?: 'Menunggu Panggilan') }}
                </div>
                <div class="text-sm font-semibold text-teal-200 pt-2" x-text="'Menuju: ' + destination"></div>
            </div>

            <!-- Call Again Sound Trigger Button -->
            <div class="pt-4 border-t border-slate-800 flex justify-center gap-4">
                <button type="button" 
                        @click="speakCall(currentNumber, currentName, destination)"
                        class="px-8 py-3.5 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-sm shadow-lg shadow-teal-500/25 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" /></svg>
                    <span>Panggil Ulang Suara (TTS)</span>
                </button>
            </div>
        </div>

        <!-- Right: Waiting Queue List to Call (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Daftar Antrean Siap Dipanggil</h3>
                    <p class="text-xs text-slate-500">Peserta yang telah check-in di lokasi</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700">
                    {{ $waitingList->count() }} Antrean
                </span>
            </div>

            <div class="space-y-2.5 max-h-[500px] overflow-y-auto">
                @forelse($waitingList as $waitReg)
                    <div class="p-3.5 rounded-2xl border border-slate-100 hover:border-teal-300 bg-slate-50/70 flex items-center justify-between gap-3 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-teal-600 text-white font-black font-mono text-base flex items-center justify-center shrink-0 shadow-xs">
                                {{ $waitReg->queue_code }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $waitReg->user->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $waitReg->user->profile?->nim }} &middot; {{ $waitReg->user->profile?->faculty }}</div>
                            </div>
                        </div>

                        <button type="button" 
                                @click="callQueue({{ $waitReg->id }}, '{{ $waitReg->queue_code }}', '{{ addslashes($waitReg->user->name) }}')"
                                class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" /></svg>
                            <span>Panggil</span>
                        </button>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada antrean yang menunggu di sesi ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
