<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kesehatan - {{ $user->profile->nim }}</title>
    <style>
        @page {
            margin: 1.5cm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1e293b;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #0d9488;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header-title h3 {
            margin: 3px 0;
            font-size: 12pt;
            color: #0d9488;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 0;
            font-size: 8pt;
            color: #64748b;
        }
        .cert-title {
            text-align: center;
            margin: 20px 0 16px;
        }
        .cert-title h1 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cert-title .cert-no {
            font-size: 9.5pt;
            color: #475569;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10pt;
        }
        .data-table .label {
            width: 28%;
            color: #475569;
        }
        .data-table .colon {
            width: 2%;
        }
        .data-table .value {
            width: 70%;
            font-weight: bold;
            color: #0f172a;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        .results-table th, .results-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 9.5pt;
        }
        .results-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
            color: #0f172a;
        }
        .badge-pass {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8.5pt;
            display: inline-block;
        }
        .statement-box {
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
            padding: 10px 14px;
            border-radius: 6px;
            margin: 16px 0;
            font-size: 10pt;
            line-height: 1.5;
        }
        .signatures-table {
            width: 100%;
            margin-top: 25px;
        }
        .signatures-table td {
            vertical-align: top;
            width: 50%;
        }
        .qr-box {
            text-align: center;
        }
        .qr-box p {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 4px;
        }
        .signature-box {
            text-align: right;
            padding-right: 20px;
        }
        .signature-box .date {
            font-size: 9.5pt;
            margin-bottom: 45px;
        }
        .signature-box .doctor-name {
            font-weight: bold;
            font-size: 10.5pt;
            text-decoration: underline;
        }
        .signature-box .nip {
            font-size: 8.5pt;
            color: #475569;
        }
    </style>
</head>
<body>

    <!-- Official Letterhead -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: center;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #0d9488; color: #ffffff; text-align: center; line-height: 60px; font-weight: bold; font-size: 22pt; margin: 0 auto;">
                    +
                </div>
            </td>
            <td class="header-title" style="width: 85%;">
                <h2>Kementerian Agama Republik Indonesia</h2>
                <h3>Klinik Pratama Rawat Jalan — Sipkesma</h3>
                <p>Universitas Islam Negeri Siber Syekh Nurjati &middot; Gedung Pusat Layanan Kesehatan Kampus Terpadu</p>
                <p>Jl. Perjuangan By Pass, Cirebon / Palembang &middot; Email: klinik@uinssc.ac.id &middot; Laman: sipkesma.uinssc.ac.id</p>
            </td>
        </tr>
    </table>

    <!-- Certificate Title -->
    <div class="cert-title">
        <h1>Surat Keterangan Kesehatan PPKMB</h1>
        <div class="cert-no">Nomor: {{ $certificateNo }}</div>
    </div>

    <p style="margin-bottom: 12px; font-size: 10pt;">
        Dokter Pemeriksa pada Klinik Pelayanan Kesehatan Mahasiswa (Sipkesma) dengan ini menerangkan bahwa telah melakukan pemeriksaan medis menyeluruh terhadap mahasiswa:
    </p>

    <!-- Student Data -->
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->profile->full_name }}</td>
        </tr>
        <tr>
            <td class="label">NIM / No. Pendaftaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->profile->nim }}</td>
        </tr>
        <tr>
            <td class="label">Fakultas / Program Studi</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->profile->faculty }} / {{ $user->profile->major }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin / Tgl Lahir</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->profile->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} / {{ \Carbon\Carbon::parse($user->profile->birth_date)->locale('id')->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="label">Golongan Darah / Rhesus</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->healthHistory->blood_type }} (Rhesus: {{ $user->healthHistory->rhesus }})</td>
        </tr>
        <tr>
            <td class="label">TB / BB / Indeks BMI</td>
            <td class="colon">:</td>
            <td class="value">{{ $user->healthHistory->height_cm }} cm / {{ $user->healthHistory->weight_kg }} kg (BMI: {{ $user->healthHistory->bmi }} - {{ $user->healthHistory->bmi_status }})</td>
        </tr>
    </table>

    <!-- 3 Station Examination Results -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Parameter Pemeriksaan</th>
                <th style="width: 20%;">Hasil Evaluasi</th>
                <th style="width: 40%;">Catatan & Keterangan Dokter</th>
            </tr>
        </thead>
        <tbody>
            @php
                $urin = $registration->getStationResult('urin');
                $napza = $registration->getStationResult('napza');
                $fisik = $registration->getStationResult('pemeriksaan');
            @endphp
            <tr>
                <td style="text-align: center;">1</td>
                <td><strong>Tes Urin Lengkap</strong><br><small style="color: #64748b;">Protein, Glukosa, Sedimen & Fungsi Ginjal</small></td>
                <td><span class="badge-pass">LAYAK / NORMAL</span></td>
                <td>{{ $urin?->notes ?: 'Dalam batas normal, reduksi (-), albumin (-)' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td><strong>Skrining Bebas NAPZA</strong><br><small style="color: #64748b;">6 Parameter (AMP, MET, THC, MOP, COC, BZO)</small></td>
                <td><span class="badge-pass">NEGATIF / NON-REAKTIF</span></td>
                <td>{{ $napza?->notes ?: 'Negatif dari zat adiktif dan narkotika terlarang' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td><strong>Pemeriksaan Fisik & Dokter</strong><br><small style="color: #64748b;">TTV, Visus Mata, Buta Warna, Fisik</small></td>
                <td><span class="badge-pass">SEHAT / LAYAK</span></td>
                <td>{{ $fisik?->notes ?: 'Tensi normal, buta warna negatif, fisik prima' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Doctor Conclusion Statement -->
    <div class="statement-box">
        Berdasarkan hasil pemeriksaan klinis 3 (tiga) stasiun medis di atas, yang bersangkutan dinyatakan:
        <div style="font-weight: bold; font-size: 11pt; color: #0f766e; text-align: center; margin-top: 4px; text-transform: uppercase;">
            SEHAT & LAYAK (FIT) MENGIKUTI SELURUH KEGIATAN PPKMB SERTA PERKULIAHAN
        </div>
    </div>

    <!-- Signatures and QR Code Validation -->
    <table class="signatures-table">
        <tr>
            <td class="qr-box">
                @if($qrBase64)
                    <img src="{{ $qrBase64 }}" width="95" height="95" alt="QR Validasi">
                @else
                    <div style="width: 95px; height: 95px; border: 1px dashed #cbd5e1; margin: 0 auto; line-height: 95px; font-size: 8pt; color: #94a3b8;">
                        [QR VALIDASI]
                    </div>
                @endif
                <p>Pindai QR untuk memvalidasi keaslian dokumen di sipkesma.uinssc.ac.id</p>
            </td>
            
            <td class="signature-box">
                <div class="date">Ditetapkan di: Palembang / Cirebon<br>Pada tanggal: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</div>
                <div class="doctor-name">dr. Nurul Aini, Sp.PK</div>
                <div class="nip">SIP. 446/1829/DKS/2024 &middot; Panitia Medis PPKMB</div>
            </td>
        </tr>
    </table>

</body>
</html>
