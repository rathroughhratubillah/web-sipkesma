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
            'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)' => [
                'Informatika',
                'Manajemen Pendidikan Islam',
                'Matematika',
                'Pendidikan Agama Islam',
                'Pendidikan Bahasa Arab',
                'Pendidikan Guru Madrasah Ibtidaiyah',
                'Pendidikan Islam Anak Usia Dini',
                'PJJ Pendidikan Agama Islam',
                'PJJ Pendidikan Bahasa Arab',
                'PJJ Pendidikan Guru Madrasah Ibtidaiyah',
                'Tadris Bahasa Indonesia',
                'Tadris Bahasa Inggris',
                'Tadris Biologi',
                'Tadris Ilmu Pengetahuan Sosial',
                'Tadris Kimia',
                'Tadris Matematika',
            ],
            'Fakultas Ekonomi Dan Bisnis Islam (FEBI)' => [
                'Akuntansi Syariah',
                'Bioteknologi',
                'Ekonomi Syariah',
                'Pariwisata Syariah',
                'Perbankan Syariah',
            ],
            'Fakultas Syariah' => [
                'Hukum Ekonomi Syari\'ah (Muamalah)',
                'Hukum Keluarga (Akhwalul Syaksiyah)',
                'Hukum Tatanegara Islam',
                'Ilmu Falak',
                'PJJ Hukum Keluarga',
            ],
            'Fakultas Dakwah Dan Komunikasi Islam (FDKI)' => [
                'Bimbingan dan Konseling Islam',
                'Komunikasi dan Penyiaran Islam',
                'Pengembangan Masyarakat Islam',
                'Sosiologi Agama',
            ],
            'Fakultas Ushuluddin Dan Arab' => [
                'Aqidah dan Filsafat Islam',
                'Bahasa dan Sastra Arab',
                'Ilmu Al-Qur\'an dan Tafsir',
                'Ilmu Hadis',
                'PJJ Sejarah Peradaban Islam',
                'Sejarah Peradaban Islam',
                'Tasawuf dan Psikoterapi',
            ],
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
