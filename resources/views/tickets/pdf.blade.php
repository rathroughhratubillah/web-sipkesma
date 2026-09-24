<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Antrean - {{ $registration->queue_code }}</title>
    <style>
        @page {
            margin: 1cm;
            size: a5 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
        }
        .ticket-box {
            border: 2px solid #0d9488;
            border-radius: 12px;
            padding: 16px;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 2px dashed #0d9488;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .ticket-header h2 {
            margin: 0;
            font-size: 14pt;
            color: #0d9488;
            text-transform: uppercase;
        }
        .ticket-header p {
            margin: 2px 0 0;
            font-size: 8.5pt;
            color: #64748b;
        }
        .queue-box {
            text-align: center;
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
        }
        .queue-title {
            font-size: 9pt;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
        }
        .queue-num {
            font-size: 34pt;
            font-weight: 900;
            color: #042f2e;
            margin: 4px 0;
            font-family: monospace;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }
        .table-data td {
            padding: 3px 4px;
            vertical-align: top;
        }
        .table-data .lbl {
            width: 32%;
            color: #475569;
        }
        .table-data .val {
            width: 68%;
            font-weight: bold;
            color: #0f172a;
        }
        .qr-center {
            text-align: center;
            margin: 10px 0;
        }
        .rules {
            font-size: 8pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="ticket-box">
        <div class="ticket-header">
            <h2>Klinik Sipkesma PPKMB UINSSC</h2>
            <p>Kartu Bukti Antrean & Check-in Skrining Kesehatan</p>
        </div>

        <div class="queue-box">
            <div class="queue-title">Nomor Urut Antrean</div>
            <div class="queue-num">{{ $registration->queue_code ?? 'A-001' }}</div>
            <div style="font-size: 8pt; color: #64748b;">Lokasi: Klinik Kampus - Gedung Poliklinik Terpadu</div>
        </div>

        <table class="table-data">
            <tr>
                <td class="lbl">Nama Mahasiswa</td>
                <td>:</td>
                <td class="val">{{ $user->profile->full_name }}</td>
            </tr>
            <tr>
                <td class="lbl">NISN</td>
                <td>:</td>
                <td class="val">{{ $user->profile->nim }}</td>
            </tr>
            <tr>
                <td class="lbl">Fakultas / Prodi</td>
                <td>:</td>
                <td class="val">{{ $user->profile->faculty }} / {{ $user->profile->major }}</td>
            </tr>
            <tr>
                <td class="lbl">Jadwal Sesi</td>
                <td>:</td>
                <td class="val">{{ \Carbon\Carbon::parse($registration->testSession->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') }} ({{ substr($registration->testSession->start_time, 0, 5) }} WIB)</td>
            </tr>
        </table>

        <div class="qr-center">
            @if($qrBase64)
                <img src="{{ $qrBase64 }}" width="110" height="110" alt="QR Ticket">
            @endif
            <div style="font-size: 8pt; font-family: monospace; color: #475569; margin-top: 4px;">
                {{ $registration->queue_code }} &middot; {{ $user->profile->nim }}
            </div>
        </div>

        <div class="rules">
            <strong>Petunjuk:</strong> Harap hadir 15 menit sebelum jam sesi dimulai. Tunjukkan barcode/QR ini pada meja check-in petugas medis di lokasi tes.
        </div>
    </div>

</body>
</html>
