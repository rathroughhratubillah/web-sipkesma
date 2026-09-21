<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $faculties = [
            'Sains dan Teknologi' => ['Sistem Informasi', 'Teknik Informatika', 'Biologi', 'Kimia', 'Matematika'],
            'Kedokteran dan Ilmu Kesehatan' => ['Pendidikan Dokter', 'Farmasi', 'Ilmu Keperawatan', 'Kesehatan Masyarakat'],
            'Ekonomi dan Bisnis Islam' => ['Manajemen Bisnis Syariah', 'Perbankan Syariah', 'Ekonomi Syariah', 'Akuntansi Syariah'],
            'Tarbiyah dan Keguruan' => ['Pendidikan Agama Islam', 'Pendidikan Bahasa Arab', 'Pendidikan Guru Madrasah'],
            'Syariah dan Hukum' => ['Hukum Keluarga Islam', 'Hukum Ekonomi Syariah', 'Ilmu Hukum'],
            'Ushuluddin dan Pemikiran Islam' => ['Ilmu Al-Qur\'an dan Tafsir', 'Aqidah dan Filsafat Islam'],
        ];

        return view('student.profile', compact('user', 'profile', 'faculties'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:30', 'unique:profiles,nim,' . ($user->profile?->id ?? 'NULL')],
            'full_name' => ['required', 'string', 'max:255'],
            'faculty' => ['required', 'string', 'max:255'],
            'major' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        Registration::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'draft']
        );

        return redirect()->route('student.health_history')->with('success', 'Data diri berhasil disimpan! Lanjutkan ke pengisian riwayat kesehatan.');
    }
}
