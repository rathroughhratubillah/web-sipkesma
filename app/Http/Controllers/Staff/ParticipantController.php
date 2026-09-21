<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $faculty = $request->get('faculty');

        $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('queue_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.profile', function ($pq) use ($search) {
                        $pq->where('full_name', 'like', "%{$search}%")
                           ->orWhere('nim', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($faculty && $faculty !== 'all') {
            $query->whereHas('user.profile', function ($pq) use ($faculty) {
                $pq->where('faculty', $faculty);
            });
        }

        $participants = $query->latest()->paginate(20)->withQueryString();

        $faculties = [
            'Sains dan Teknologi',
            'Kedokteran dan Ilmu Kesehatan',
            'Ekonomi dan Bisnis Islam',
            'Tarbiyah dan Keguruan',
            'Syariah dan Hukum',
            'Ushuluddin dan Pemikiran Islam',
        ];

        return view('staff.participants.index', compact('participants', 'search', 'status', 'faculty', 'faculties'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'Rekap_Peserta_Sipkesma_' . date('Y-m-d_His') . '.csv';

        $participants = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults'])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($participants) {
            $file = fopen('php://output', 'w');
            // Add BOM for Indonesian UTF-8 Excel support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID Registrasi',
                'Nomor Antrean',
                'NIM',
                'Nama Lengkap',
                'Email',
                'Fakultas',
                'Program Studi',
                'No. WhatsApp',
                'Jenis Kelamin',
                'Golongan Darah',
                'BMI',
                'Status Gizi',
                'Sesi Skrining',
                'Status Pembayaran',
                'Status Skrining',
                'Hasil Tes Urin',
                'Hasil Tes NAPZA',
                'Hasil Tes Fisik',
                'Waktu Check-in',
                'Waktu Selesai',
            ]);

            foreach ($participants as $reg) {
                $profile = $reg->user?->profile;
                $health = $reg->user?->healthHistory;
                $urin = $reg->getStationResult('urin')?->status ?? 'Belum';
                $napza = $reg->getStationResult('napza')?->status ?? 'Belum';
                $fisik = $reg->getStationResult('pemeriksaan')?->status ?? 'Belum';

                fputcsv($file, [
                    $reg->id,
                    $reg->queue_code ?? '-',
                    $profile?->nim ?? '-',
                    $profile?->full_name ?? $reg->user?->name,
                    $reg->user?->email,
                    $profile?->faculty ?? '-',
                    $profile?->major ?? '-',
                    $profile?->phone ?? '-',
                    $profile?->gender ?? '-',
                    ($health?->blood_type ?? '-') . ($health?->rhesus ?? ''),
                    $health?->bmi ?? '-',
                    $health?->bmi_status ?? '-',
                    $reg->testSession?->session_name ?? '-',
                    $reg->payment?->status ?? 'Belum Bayar',
                    $reg->status,
                    $urin,
                    $napza,
                    $fisik,
                    $reg->checked_in_at?->format('d/m/Y H:i') ?? '-',
                    $reg->completed_at?->format('d/m/Y H:i') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
