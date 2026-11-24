<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantController extends Controller
{
    public static function getFacultyMajorMap(): array
    {
        return [
            'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)' => [
                'Informatika',
                'Manajemen Pendidikan Islam',
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
                "Hukum Ekonomi Syari'ah (Muamalah)",
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
                "Ilmu Al-Qur'an dan Tafsir",
                'Ilmu Hadis',
                'PJJ Sejarah Peradaban Islam',
                'Sejarah Peradaban Islam',
                'Tasawuf dan Psikoterapi',
            ],
        ];
    }

    public function index(Request $request)
    {
        $facultyMajorMap = self::getFacultyMajorMap();
        $faculties = array_keys($facultyMajorMap);

        $selectedFaculty = $request->get('faculty');
        $selectedMajor   = $request->get('major');
        $search          = $request->get('search');
        $status          = $request->get('status');
        $selectedYear    = $request->get('year', 'all');
        $availableYears  = Registration::getAvailableYears();

        // Validasi fakultas jika diberikan
        if ($selectedFaculty && !array_key_exists($selectedFaculty, $facultyMajorMap)) {
            $selectedFaculty = null;
        }

        // Tentukan View Level (Perhalaman dipisah sesuai permintaan user):
        // 1. 'faculty' -> Halaman 1: Tampilkan HANYA daftar Fakultas
        // 2. 'major'   -> Halaman 2: Tampilkan HANYA daftar Jurusan di Fakultas terpilih
        // 3. 'student' -> Halaman 3: Tampilkan HANYA Tabel Mahasiswa pada Jurusan terpilih
        // 4. 'search'  -> Halaman Hasil Pencarian jika user mengetik keyword tanpa jurusan
        if ($selectedFaculty && $selectedMajor) {
            $viewLevel = 'student';
        } elseif ($selectedFaculty) {
            $viewLevel = 'major';
        } elseif ($search) {
            $viewLevel = 'search';
        } else {
            $viewLevel = 'faculty';
        }

        // Data untuk Halaman 1 (Level Fakultas)
        $facultyCountsQuery = Profile::select('faculty', DB::raw('count(*) as total'))
            ->whereNotNull('faculty')
            ->where('faculty', '!=', '');

        if ($selectedYear && $selectedYear !== 'all') {
            $facultyCountsQuery->whereHas('user.registration', fn($rq) => $rq->where('academic_year', $selectedYear));
        }

        $facultyCountsRaw = $facultyCountsQuery
            ->groupBy('faculty')
            ->pluck('total', 'faculty')
            ->toArray();

        $facultyStats = [];
        $totalAllParticipants = 0;
        foreach ($faculties as $fac) {
            $cnt = $facultyCountsRaw[$fac] ?? 0;
            $facultyStats[$fac] = $cnt;
            $totalAllParticipants += $cnt;
        }

        // Data untuk Halaman 2 (Level Jurusan) jika ada fakultas yang dipilih
        $currentMajors = [];
        $majorStats = [];
        if ($selectedFaculty) {
            $currentMajors = $facultyMajorMap[$selectedFaculty] ?? [];
            $majorCountsQuery = Profile::select('major', DB::raw('count(*) as total'))
                ->where('faculty', $selectedFaculty)
                ->whereNotNull('major')
                ->where('major', '!=', '');

            if ($selectedYear && $selectedYear !== 'all') {
                $majorCountsQuery->whereHas('user.registration', fn($rq) => $rq->where('academic_year', $selectedYear));
            }

            $majorCountsRaw = $majorCountsQuery
                ->groupBy('major')
                ->pluck('total', 'major')
                ->toArray();

            foreach ($currentMajors as $maj) {
                $majorStats[$maj] = $majorCountsRaw[$maj] ?? 0;
            }
        }

        // Data untuk Halaman 3 (Tabel Mahasiswa) atau Pencarian
        $participants = null;

        if ($viewLevel === 'student') {
            $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults'])
                ->whereHas('user.profile', function ($pq) use ($selectedFaculty, $selectedMajor) {
                    $pq->where('faculty', $selectedFaculty)
                       ->where('major', $selectedMajor);
                });

            if ($selectedYear && $selectedYear !== 'all') {
                $query->where('academic_year', $selectedYear);
            }

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

            $participants = $query->latest()->paginate(25)->withQueryString();
        } elseif ($viewLevel === 'search') {
            $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults']);

            if ($selectedYear && $selectedYear !== 'all') {
                $query->where('academic_year', $selectedYear);
            }

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

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            $participants = $query->latest()->paginate(25)->withQueryString();
        }

        return view('staff.participants.index', compact(
            'viewLevel',
            'facultyMajorMap',
            'faculties',
            'selectedFaculty',
            'selectedMajor',
            'facultyStats',
            'majorStats',
            'currentMajors',
            'participants',
            'search',
            'status',
            'totalAllParticipants',
            'selectedYear',
            'availableYears'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $faculty      = $request->get('faculty');
        $major        = $request->get('major');
        $selectedYear = $request->get('year', 'all');

        $suffix = '';
        if ($selectedYear && $selectedYear !== 'all') {
            $suffix .= '_Tahun_' . $selectedYear;
        }
        if ($major && $major !== 'all') {
            $suffix .= '_' . Str::slug($major);
        } elseif ($faculty && $faculty !== 'all') {
            $suffix .= '_' . Str::slug($faculty);
        }

        $fileName = 'Rekap_Peserta_Sipkesma' . $suffix . '_' . date('Y-m-d_His') . '.csv';

        $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults']);

        if ($selectedYear && $selectedYear !== 'all') {
            $query->where('academic_year', $selectedYear);
        }

        if ($faculty && $faculty !== 'all') {
            $query->whereHas('user.profile', fn($q) => $q->where('faculty', $faculty));
        }

        if ($major && $major !== 'all') {
            $query->whereHas('user.profile', fn($q) => $q->where('major', $major));
        }

        $participants = $query->get();

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
                'Tahun Akademik',
                'Nomor Antrean',
                'NISN',
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
                'Hasil Tes Buta Warna',
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
                    $reg->academic_year ?? '-',
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
