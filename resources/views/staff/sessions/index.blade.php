@extends('layouts.staff')

@section('title', 'Kelola Kuota Jadwal')
@section('header_title', 'Manajemen Jadwal Sesi & Kuota Skrining')

@section('content')
<div class="space-y-6" x-data="{
    createModal: false,
    editModal: false,
    editAction: '',
    editSessionName: '',
    editDate: '',
    editStart: '',
    editEnd: '',
    editQuota: 100,
    editActive: '1',

    openEdit(session) {
        this.editAction = '/petugas/jadwal/' + session.id;
        this.editSessionName = session.session_name;
        this.editDate = session.session_date ? session.session_date.substring(0, 10) : '';
        this.editStart = session.start_time;
        this.editEnd = session.end_time;
        this.editQuota = session.quota;
        this.editActive = session.is_active ? '1' : '0';
        this.editModal = true;
    }
}">

    <!-- Top Action -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Sesi Skrining PPKMB</h2>
            <p class="text-xs text-slate-500">Atur kapasitas kuota dan waktu pelaksanaan tes kesehatan per gelombang.</p>
        </div>
        <button type="button" 
                @click="createModal = true"
                class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            <span>Tambah Sesi Baru</span>
        </button>
    </div>

    <!-- Sessions Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4">Tanggal Pelaksanaan</th>
                        <th class="p-4">Nama Sesi</th>
                        <th class="p-4">Jam Pelaksanaan</th>
                        <th class="p-4">Kuota Total</th>
                        <th class="p-4">Terisi</th>
                        <th class="p-4">Sisa Kuota</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($session->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $session->session_name }}</div>
                            </td>
                            <td class="p-4 text-slate-700 font-mono">
                                {{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB
                            </td>
                            <td class="p-4 font-bold text-slate-900 font-mono text-sm">
                                {{ $session->quota }}
                            </td>
                            <td class="p-4 font-semibold text-teal-700 font-mono">
                                {{ $session->registrations_count }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $session->remaining_quota > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $session->remaining_quota }} slot
                                </span>
                            </td>
                            <td class="p-4">
                                @if($session->is_active)
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" 
                                            @click="openEdit({{ json_encode($session) }})"
                                            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('staff.sessions.destroy', $session) }}" onsubmit="return confirm('Hapus sesi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-[11px] transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Belum ada sesi jadwal yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Sesi -->
    <div x-show="createModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-100" @click.outside="createModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Sesi Skrining Baru</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <form method="POST" action="{{ route('staff.sessions.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Sesi <span class="text-rose-500">*</span></label>
                    <input type="text" name="session_name" required placeholder="Contoh: Sesi Pagi Gelombang 3"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Sesi <span class="text-rose-500">*</span></label>
                        <input type="date" name="session_date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kapasitas Kuota <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota" required value="100" min="1"
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-mono outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" required value="08:00"
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" required value="10:00"
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Status Aktif</label>
                    <select name="is_active" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <option value="1">Aktif (Dapat Dipilih Mahasiswa)</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20">
                        Simpan Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Sesi -->
    <div x-show="editModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-100" @click.outside="editModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Perbarui Sesi Skrining</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <form :action="editAction" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Sesi <span class="text-rose-500">*</span></label>
                    <input type="text" name="session_name" x-model="editSessionName" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Sesi <span class="text-rose-500">*</span></label>
                        <input type="date" name="session_date" x-model="editDate" required
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kapasitas Kuota <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota" x-model="editQuota" required min="1"
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs font-mono outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" x-model="editStart" required
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" x-model="editEnd" required
                               class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:border-teal-500 text-xs outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Status Aktif</label>
                    <select name="is_active" x-model="editActive" class="w-full px-4 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                        <option value="1">Aktif (Dapat Dipilih Mahasiswa)</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20">
                        Perbarui Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
