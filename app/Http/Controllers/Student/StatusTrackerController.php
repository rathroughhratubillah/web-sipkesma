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
                'title' => '1. Tes Urin (Laboratorium)',
                'desc' => 'Pemeriksaan parameter urin, glukosa, protein, dan sedimen.',
                'icon' => 'flask',
            ],
            'napza' => [
                'title' => '2. Tes Skrining Bebas NAPZA',
                'desc' => 'Uji 6 parameter zat adiktif & narkotika (Amphetamine, THC, Morphine, dll).',
                'icon' => 'shield',
            ],
            'pemeriksaan' => [
                'title' => '3. Cek Buta Warna',
                'desc' => 'Tekanan darah, visus mata, buta warna, dan riwayat kesehatan oleh dokter.',
                'icon' => 'stethoscope',
            ],
        ];

        return view('student.status', compact('user', 'registration', 'stations'));
    }
}
