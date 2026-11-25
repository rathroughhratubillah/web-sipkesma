<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Antrean - {{ $registration->queue_code }}</title>
    <style>
        @page {
            margin: 0.8cm;
            size: a5 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Outer Ticket Container */
        .ticket-card {
            border: 2px solid #0f766e;
            border-radius: 12px;
            overflow: hidden;
            background-color: #ffffff;
        }

        /* Top Header Banner */
        .ticket-header {
            background-color: #0f766e;
            color: #ffffff;
            padding: 12px 16px;
            border-bottom: 2px solid #115e59;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
            color: #ffffff;
        }
        .brand-sub {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ccfbf1;
            margin-top: 2px;
        }
        .badge-verified {
            display: inline-block;
            background-color: #34d399;
            color: #064e3b;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Body Two Columns */
        .body-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-column {
            width: 67%;
            padding: 14px 16px;
            vertical-align: top;
        }
        .stub-column {
            width: 33%;
            padding: 14px 12px;
            vertical-align: top;
            background-color: #f8fafc;
            border-left: 2px dashed #94a3b8;
            text-align: center;
        }

        /* Section Headings */
        .section-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f766e;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        /* Student Data Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .info-label {
            width: 32%;
            color: #64748b;
        }
        .info-sep {
            width: 4%;
            color: #94a3b8;
            text-align: center;
        }
        .info-val {
            width: 64%;
            color: #0f172a;
            font-weight: bold;
        }

        /* Checkbox 3 Stations */
        .station-box {
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .station-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
        }
        .station-table td {
            padding: 2px 0;
            color: #334155;
        }
        .chk-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1.5px solid #0f766e;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
            background-color: #ffffff;
        }

        /* Simulated 1D Barcode */
        .barcode-wrap {
            text-align: center;
            padding-top: 6px;
        }
        .barcode-bars {
            height: 34px;
            letter-spacing: 1.5px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 26pt;
            color: #0f172a;
            line-height: 0.8;
            overflow: hidden;
        }
        .barcode-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8pt;
            font-weight: bold;
            color: #475569;
            letter-spacing: 2px;
            margin-top: 3px;
        }

        /* Stub Section (Right) */
        .stub-badge {
            font-size: 7.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .queue-display {
            background-color: #ffffff;
            border: 2px solid #0f766e;
            border-radius: 8px;
            padding: 10px 4px;
            margin: 6px 0 10px 0;
        }
        .queue-number {
            font-size: 30pt;
            font-weight: 900;
            color: #0f766e;
            font-family: 'Courier New', Courier, monospace;
            line-height: 1;
            margin: 0;
        }
        .queue-caption {
            font-size: 6.8pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .stub-info-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px;
            font-size: 7.2pt;
            color: #334155;
            text-align: left;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .stamp-box {
            display: inline-block;
            border: 2px solid #059669;
            color: #059669;
            font-weight: 900;
            font-size: 7.5pt;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Bottom Rules & Footer */
        .ticket-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 8px 16px;
            font-size: 7pt;
            color: #64748b;
        }
        .ticket-footer strong {
            color: #334155;
        }
    </style>
</head>
<body>

    <div class="ticket-card">
        
        <!-- Header -->
        <div class="ticket-header">
            <table class="header-table">
                <tr>
                    <td style="width: 70%;">
                        <div class="brand-title">SIPKESMA UINSSC</div>
                        <div class="brand-sub">Sistem Pelayanan Kesehatan Mahasiswa Baru PPKMB</div>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                        <span class="badge-verified">&#10003; TERVERIFIKASI &amp; LUNAS</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Ticket Body (2 Columns: Main Info + Stub Sobekan) -->
        <table class="body-table">
            <tr>
                
                <!-- Left: Main Participant Info -->
                <td class="main-column">
                    
                    <div class="section-label">Identitas Mahasiswa Peserta</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Nama Lengkap</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $user->profile->full_name ?? $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">NIM / ID</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $user->profile->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Fakultas / Prodi</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $user->profile->faculty ?? '-' }} &bull; {{ $user->profile->major ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Jadwal Skrining</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">
                                @if($registration->testSession)
                                    {{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                @else
                                    Sesuai Jadwal Pilihan
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Sesi &amp; Waktu</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">
                                @if($registration->testSession)
                                    {{ substr($registration->testSession->start_time, 0, 5) }} - {{ substr($registration->testSession->end_time, 0, 5) }} WIB
                                @else
                                    Sesi Terjadwal
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Lokasi Tes</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">Klinik Kampus - Gedung Layanan Medis Terpadu</td>
                        </tr>
                    </table>

                    <div class="section-label">Tahapan 3 Stasiun Skrining Medis</div>
                    <div class="station-box">
                        <table class="station-table">
                            <tr>
                                <td style="width: 33%;"><span class="chk-box"></span> <strong>1. Tes Buta Warna</strong></td>
                                <td style="width: 33%;"><span class="chk-box"></span> <strong>2. Tes NAPZA</strong></td>
                                <td style="width: 34%;"><span class="chk-box"></span> <strong>3. Fisik &amp; Tensi</strong></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 1D Code 128B Scannable Barcode -->
                    <div class="barcode-wrap">
                        <div style="display: inline-block; background-color: #ffffff; padding: 4px 6px; border: 1px solid #e2e8f0; border-radius: 4px;">
                            @if(!empty($barcodeHtml))
                                {!! $barcodeHtml !!}
                            @else
                                {!! app(\App\Services\BarcodeService::class)->toHtmlTable($registration->ticket_barcode_code, 38, 1) !!}
                            @endif
                        </div>
                        <div class="barcode-code">{{ $barcodeCode ?? $registration->ticket_barcode_code }}</div>
                    </div>

                </td>

                <!-- Right: Tear-off Stub (Sobekan Petugas) -->
                <td class="stub-column">
                    <div class="stub-badge">Kupon Antrean</div>

                    <div class="queue-display">
                        <div class="queue-number">{{ $registration->queue_code ?? 'A-001' }}</div>
                        <div class="queue-caption">Nomor Urut Panggilan</div>
                    </div>

                    <div class="stub-info-box">
                        <div><strong>Sesi:</strong> {{ $registration->testSession->session_name ?? 'Sesi 1' }}</div>
                        <div style="margin-top: 3px;"><strong>Jam:</strong> {{ substr($registration->testSession->start_time ?? '08:00', 0, 5) }} WIB</div>
                        <div style="margin-top: 3px;"><strong>Pintu:</strong> Jalur Pemeriksaan A</div>
                    </div>

                    <div style="margin-top: 6px;">
                        <span class="stamp-box">&#10003; SIPKESMA VALID</span>
                    </div>

                    <div style="font-size: 6.5pt; color: #94a3b8; margin-top: 8px;">
                        Sobek / tunjukkan bagian ini kepada petugas meja stasiun
                    </div>
                </td>

            </tr>
        </table>

        <!-- Footer Instructions -->
        <div class="ticket-footer">
            <strong>Petunjuk Penting Mahasiswa Baru:</strong>
            Hadir 15 menit sebelum sesi pemeriksaan dimulai. Tunjukkan kartu bukti ini atau nomor antrean kepada petugas pendaftaran di klinik. Pastikan kondisi tubuh dalam keadaan prima.
        </div>

    </div>

</body>
</html>
