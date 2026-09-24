<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'healthHistory', 'registration.testSession', 'registration.payment', 'registration.stationResults']);

        $registration = $user->registration;
        if (!$registration) {
            $registration = Registration::create([
                'user_id' => $user->id,
                'status' => 'draft',
            ]);
            $user->load('registration.testSession', 'registration.payment', 'registration.stationResults');
        }

        // Determine step progression
        $steps = [
            1 => [
                'id' => 1,
                'name' => 'Registrasi Akun',
                'description' => 'Akun terdaftar dan email tervalidasi.',
                'is_completed' => true,
                'route' => null,
            ],
            2 => [
                'id' => 2,
                'name' => 'Lengkapi Data Diri',
                'description' => 'Isi identitas diri, NISN, dan prodi.',
                'is_completed' => (bool) $user->profile,
                'route' => route('student.profile'),
            ],
            3 => [
                'id' => 3,
                'name' => 'Riwayat Kesehatan',
                'description' => 'Data medis & kalkulasi status gizi BMI.',
                'is_completed' => (bool) $user->healthHistory,
                'route' => route('student.health_history'),
            ],
            4 => [
                'id' => 4,
                'name' => 'Pilih Jadwal Tes',
                'description' => 'Pilih sesi skrining sesuai kuota.',
                'is_completed' => (bool) $registration->test_session_id,
                'route' => route('student.schedule'),
            ],
            5 => [
                'id' => 5,
                'name' => 'Konfirmasi Daftar',
                'description' => 'Kunci data pendaftaran.',
                'is_completed' => in_array($registration->status, ['awaiting_payment', 'awaiting_verification', 'cleared', 'checked_in', 'in_progress', 'completed']),
                'route' => route('student.confirmation'),
            ],
            6 => [
                'id' => 6,
                'name' => 'Bayar Biaya Skrining',
                'description' => 'Transfer biaya skrining PPKMB.',
                'is_completed' => (bool) ($registration->payment && in_array($registration->payment->status, ['pending', 'verified'])),
                'route' => route('student.payment'),
            ],
            7 => [
                'id' => 7,
                'name' => 'Verifikasi Bayar',
                'description' => 'Petugas memverifikasi bukti bayar.',
                'is_completed' => (bool) ($registration->payment && $registration->payment->status === 'verified'),
                'route' => route('student.payment'),
            ],
            8 => [
                'id' => 8,
                'name' => 'Nomor Urut Tes',
                'description' => 'Dapatkan QR tiket antrean digital.',
                'is_completed' => in_array($registration->status, ['cleared', 'checked_in', 'in_progress', 'completed']),
                'route' => route('student.ticket'),
            ],
            9 => [
                'id' => 9,
                'name' => 'Check-in Kampus',
                'description' => 'Pindai QR saat tiba di klinik kampus.',
                'is_completed' => in_array($registration->status, ['checked_in', 'in_progress', 'completed']),
                'route' => route('student.status'),
            ],
            10 => [
                'id' => 10,
                'name' => 'Pemeriksaan 3 Stasiun',
                'description' => 'Tes urin, NAPZA, dan fisik.',
                'is_completed' => $registration->isAllStationsPassed(),
                'route' => route('student.status'),
            ],
            11 => [
                'id' => 11,
                'name' => 'Unduh Sertifikat',
                'description' => 'Sertifikat resmi tanda lulus skrining.',
                'is_completed' => $registration->status === 'completed',
                'route' => route('student.certificate'),
            ],
        ];

        // Find current active step
        $currentStepId = 2;
        foreach ($steps as $id => $step) {
            if (!$step['is_completed']) {
                $currentStepId = $id;
                break;
            }
            if ($id === 11 && $step['is_completed']) {
                $currentStepId = 11;
            }
        }

        $nextStep = $steps[$currentStepId] ?? $steps[2];

        // Format summary strings
        $statusLabels = [
            'draft' => 'Belum Konfirmasi',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'awaiting_verification' => 'Menunggu Verifikasi',
            'cleared' => 'Siap Hadir (Tiket Terbit)',
            'checked_in' => 'Hadir di Lokasi',
            'in_progress' => 'Sedang Diperiksa',
            'completed' => 'Selesai & Lulus',
        ];

        $statusText = $statusLabels[$registration->status] ?? 'Belum Konfirmasi';

        return view('student.beranda', compact('user', 'registration', 'steps', 'currentStepId', 'nextStep', 'statusText'));
    }
}
