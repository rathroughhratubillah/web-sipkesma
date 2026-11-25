@extends('layouts.dokter')

@section('title', 'Rekam Medis Mahasiswa')
@section('header_title', 'Detail Data Diri & Riwayat Kesehatan')
@section('header_subtitle', 'Informasi identitas lengkap dan riwayat kesehatan mahasiswa peserta PPKMB')

@section('content')
<div class="max-w-5xl mx-auto space-y-5" x-data="{
    record: @js($medicalPayload),
    confirmOnSiteCheckin: null
}">

    <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('dokter.patients.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition">
            <span>&larr; Kembali ke Daftar Mahasiswa</span>
        </a>

        <a href="{{ route('dokter.scan', ['code' => $registration->ticket_barcode_code]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 font-bold text-xs transition">
            <span>Buka di Mode Scan Di Tempat</span>
        </a>
    </div>

    @include('dokter.partials.patient-record-panel')

</div>
@endsection

