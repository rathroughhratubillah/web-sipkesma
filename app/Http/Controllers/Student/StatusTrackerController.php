<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusTrackerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['profile', 'registration.testSession', 'registration.stationResults']);
        $registration = $user->registration;

        $stations = [
            'urin' => [
                'title' => '1. Tes Buta Warna (Ishihara)',
                'desc' => 'Pemeriksaan persepsi warna menggunakan buku Ishihara 38 plates untuk mendeteksi buta warna.',
                'icon' => 'eye',
            ],
            'napza' => [
                'title' => '2. Tes Skrining Bebas NAPZA (6 Parameter)',
                'desc' => 'Uji spesimen skrining zat adiktif & narkotika (AMP, MET, THC, MOP, COC, BZO).',
                'icon' => 'shield',
            ],
            'pemeriksaan' => [
                'title' => '3. Pemeriksaan Fisik & Dokter',
                'desc' => 'Pemeriksaan tanda vital (tensi, TB, BB) dan konsultasi medis dengan dokter klinik.',
                'icon' => 'stethoscope',
            ],
        ];

        return view('student.status', compact('user', 'registration', 'stations'));
    }
}
