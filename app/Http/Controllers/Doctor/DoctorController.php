<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Staff\ParticipantController;
use App\Models\Profile;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Halaman utama Portal Dokter / Tim Kesehatan: statistik medis + scan cepat + ringkasan peserta.
     */
    public function dashboard(Request $request)
    {
        $selectedYear = $request->get('year', 'all');
        $availableYears = Registration::getAvailableYears();

        $regQuery = Registration::whereHas('user', fn($q) => $q->role('student'));
        if ($selectedYear && $selectedYear !== 'all') {
            $regQuery->where('academic_year', $selectedYear);
        }

        $allRegs = (clone $regQuery)
            ->with(['user.profile', 'user.healthHistory', 'testSession'])
            ->get();

        $totalStudents = $allRegs->count();
        $withHealth = $allRegs->filter(fn($r) => $r->user?->healthHistory !== null)->count();
        $withoutHealth = $totalStudents - $withHealth;
        $readyOrCheckedIn = $allRegs->filter(fn($r) => in_array($r->status, ['cleared', 'checked_in', 'in_progress']))->count();
        $completedCount = $allRegs->filter(fn($r) => $r->status === 'completed')->count();

        $flaggedMedical = $allRegs->filter(function ($r) {
            $h = $r->user?->healthHistory;
            if (!$h) {
                return false;
            }
            return $this->hasClinicalNote($h->allergies) || $this->hasClinicalNote($h->chronic_diseases);
        })->count();

        $stats = [
            'total_students'   => $totalStudents,
            'with_health'      => $withHealth,
            'without_health'   => $withoutHealth,
            'ready_for_exam'   => $readyOrCheckedIn,
            'completed'        => $completedCount,
            'flagged_medical'  => $flaggedMedical,
            'completion_rate'  => $totalStudents > 0 ? round(($withHealth / $totalStudents) * 100, 1) : 0,
        ];

        $recentPatients = (clone $regQuery)
            ->with(['user.profile', 'user.healthHistory', 'testSession', 'stationResults'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('dokter.dashboard', compact(
            'stats',
            'recentPatients',
            'selectedYear',
            'availableYears'
        ));
    }

    /**
     * Halaman Pemindaian Barcode Tiket Di Tempat (USB Scanner, Kamera, atau Input Manual).
     */
    public function scan(Request $request)
    {
        $code = trim((string) $request->get('code', ''));
        $scannedPayload = null;
        $scanError = null;

        if ($code !== '') {
            $registration = $this->resolveRegistrationFromInput($code);
            if ($registration) {
                $scannedPayload = $this->formatRegistrationMedicalPayload($registration);
            } else {
                $scanError = "Data tiket atau peserta dengan kode '{$code}' tidak ditemukan.";
            }
        }

        $todayPatients = Registration::with(['user.profile', 'user.healthHistory', 'testSession'])
            ->whereIn('status', ['cleared', 'checked_in', 'in_progress', 'completed'])
            ->whereHas('user', fn($q) => $q->role('student'))
            ->latest('updated_at')
            ->take(12)
            ->get();

        return view('dokter.scan', compact('code', 'scannedPayload', 'scanError', 'todayPatients'));
    }

    /**
     * Endpoint JSON untuk lookup instan ketika barcode tiket di-scan dokter di tempat.
     */
    public function scanLookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $code = trim($validated['code']);
        $registration = $this->resolveRegistrationFromInput($code);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => "Tiket / Barcode '{$code}' tidak ditemukan. Pastikan barcode tiket (SPK-...) atau NIM mahasiswa benar.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data diri dan riwayat kesehatan peserta berhasil dimuat.',
            'data'    => $this->formatRegistrationMedicalPayload($registration),
        ]);
    }

    /**
     * Konfirmasi check-in di tempat langsung dari halaman scan dokter jika peserta berstatus 'cleared'.
     */
    public function checkInOnSite(Request $request, Registration $registration)
    {
        if (in_array($registration->status, ['draft', 'awaiting_payment', 'awaiting_verification'])) {
            $msg = 'Peserta belum menyelesaikan verifikasi pembayaran. Status saat ini: ' . $this->translateStatus($registration->status);
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if (in_array($registration->status, ['checked_in', 'in_progress', 'completed'])) {
            $msg = 'Peserta sudah tercatat check-in sebelumnya pada ' . ($registration->checked_in_at?->format('H:i') ?? '-') . ' WIB.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'already_checked_in' => true,
                    'message' => $msg,
                    'data' => $this->formatRegistrationMedicalPayload($registration->fresh(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults'])),
                ]);
            }
            return back()->with('info', $msg);
        }

        $registration->update([
            'status'        => 'checked_in',
            'checked_in_at' => Carbon::now(),
        ]);

        $fresh = $registration->fresh(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults']);
        $msg = "Kehadiran (Check-in) peserta {$fresh->user?->name} ({$fresh->queue_code}) berhasil dikonfirmasi.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'data'    => $this->formatRegistrationMedicalPayload($fresh),
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Daftar lengkap mahasiswa terdaftar beserta riwayat kesehatan (dengan filter tahun, fakultas, prodi, pencarian).
     */
    public function patients(Request $request)
    {
        $search         = $request->get('search');
        $selectedYear   = $request->get('year', 'all');
        $selectedFaculty = $request->get('faculty', 'all');
        $selectedMajor  = $request->get('major', 'all');
        $healthStatus   = $request->get('health_status', 'all');
        $regStatus      = $request->get('status', 'all');

        $availableYears = Registration::getAvailableYears();
        $faculties      = ParticipantController::getFacultyMajorMap();

        $query = Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'stationResults'])
            ->whereHas('user', fn($q) => $q->role('student'));

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

        if ($selectedFaculty && $selectedFaculty !== 'all') {
            $query->whereHas('user.profile', fn($pq) => $pq->where('faculty', $selectedFaculty));
        }

        if ($selectedMajor && $selectedMajor !== 'all') {
            $query->whereHas('user.profile', fn($pq) => $pq->where('major', $selectedMajor));
        }

        if ($healthStatus === 'completed') {
            $query->whereHas('user.healthHistory');
        } elseif ($healthStatus === 'empty') {
            $query->whereDoesntHave('user.healthHistory');
        }

        if ($regStatus && $regStatus !== 'all') {
            $query->where('status', $regStatus);
        }

        $registrations = $query->latest('updated_at')->paginate(20)->withQueryString();

        return view('dokter.patients.index', compact(
            'registrations',
            'search',
            'selectedYear',
            'availableYears',
            'faculties',
            'selectedFaculty',
            'selectedMajor',
            'healthStatus',
            'regStatus'
        ));
    }

    /**
     * Halaman detail lengkap data diri & riwayat kesehatan satu mahasiswa.
     */
    public function showPatient(Registration $registration)
    {
        $registration->load(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults']);
        $medicalPayload = $this->formatRegistrationMedicalPayload($registration);

        return view('dokter.patients.show', compact('registration', 'medicalPayload'));
    }

    /**
     * Cari registrasi dari barcode tiket, kode antrean, NIM, atau nama lengkap tunggal.
     */
    protected function resolveRegistrationFromInput(string $code): ?Registration
    {
        $registration = Registration::findByScanCode($code);
        if ($registration) {
            return $registration;
        }

        // Fallback: pencarian berdasarkan nama lengkap bila unik / tepat
        $profile = Profile::whereRaw('LOWER(full_name) = ?', [strtolower($code)])->first();
        if ($profile) {
            return Registration::with(['user.profile', 'user.healthHistory', 'testSession', 'payment', 'stationResults'])
                ->where('user_id', $profile->user_id)
                ->first();
        }

        return null;
    }

    /**
     * Susun payload terstruktur data diri + riwayat kesehatan + tiket untuk tampilan UI & AJAX.
     */
    public function formatRegistrationMedicalPayload(Registration $reg): array
    {
        $user = $reg->user;
        $prof = $user?->profile;
        $hh   = $user?->healthHistory;
        $sess = $reg->testSession;

        $ageText = null;
        $birthDateText = '-';
        if ($prof?->birth_date) {
            $birth = Carbon::parse($prof->birth_date);
            $birthDateText = $birth->locale('id')->isoFormat('D MMMM Y');
            $ageText = $birth->age . ' tahun';
        }

        $stationsMap = [
            'urin'        => '1. Tes Buta Warna',
            'napza'       => '2. Tes NAPZA',
            'pemeriksaan' => '3. Fisik & Dokter',
        ];
        $stations = [];
        foreach ($stationsMap as $key => $title) {
            $res = $reg->stationResults->firstWhere('station_name', $key);
            $stations[] = [
                'key'           => $key,
                'title'         => $title,
                'status'        => $res?->status ?? 'pending',
                'notes'         => $res?->notes,
                'examiner_name' => $res?->examiner_name,
                'examined_at'   => $res?->examined_at?->format('d/m/Y H:i'),
            ];
        }

        return [
            'registration' => [
                'id'            => $reg->id,
                'academic_year' => $reg->academic_year ?? date('Y'),
                'status'        => $reg->status,
                'status_label'  => $this->translateStatus($reg->status),
                'is_verified'   => in_array($reg->status, ['cleared', 'checked_in', 'in_progress', 'completed']),
                'queue_code'    => $reg->queue_code ?: '-',
                'barcode_code'  => $reg->ticket_barcode_code,
                'checked_in_at' => $reg->checked_in_at?->format('d/m/Y H:i') . ($reg->checked_in_at ? ' WIB' : ''),
                'session_name'  => $sess?->session_name ?? 'Belum dipilih',
                'session_date'  => $sess?->session_date ? Carbon::parse($sess->session_date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-',
                'session_time'  => $sess ? (substr($sess->start_time, 0, 5) . ' - ' . substr($sess->end_time, 0, 5) . ' WIB') : '-',
                'checkin_url'   => route('dokter.scan.checkin', $reg->id),
                'detail_url'    => route('dokter.patients.show', $reg->id),
            ],
            'profile' => [
                'full_name'    => $prof?->full_name ?: ($user?->name ?? '-'),
                'email'        => $user?->email ?? '-',
                'nim'          => $prof?->nim ?? '-',
                'faculty'      => $prof?->faculty ?? '-',
                'major'        => $prof?->major ?? '-',
                'gender'       => $prof?->gender ?? '-',
                'gender_label' => $prof?->gender === 'L' ? 'Laki-laki' : ($prof?->gender === 'P' ? 'Perempuan' : '-'),
                'birth_date'   => $birthDateText,
                'age'          => $ageText,
                'phone'        => $prof?->phone ?? '-',
                'address'      => $prof?->address ?? '-',
                'has_profile'  => $prof !== null,
            ],
            'health' => [
                'has_health'              => $hh !== null,
                'blood_type'              => $hh?->blood_type ?? '-',
                'rhesus'                  => $hh?->rhesus ?? '',
                'blood_display'           => $hh ? trim(($hh->blood_type ?? '-') . ' ' . ($hh->rhesus ?? '')) : '-',
                'height_cm'               => $hh?->height_cm,
                'weight_kg'               => $hh?->weight_kg,
                'bmi'                     => $hh?->bmi,
                'bmi_status'              => $hh?->bmi_status ?? '-',
                'is_smoker'               => $hh ? (bool) $hh->is_smoker : null,
                'smoker_label'            => $hh === null ? '-' : ($hh->is_smoker ? 'Perokok Aktif' : 'Tidak Merokok'),
                'vaccine_status'          => $hh?->vaccine_status ?? '-',
                'allergies'               => $hh?->allergies ?: 'Tidak ada',
                'has_allergy_alert'       => $hh ? $this->hasClinicalNote($hh->allergies) : false,
                'chronic_diseases'        => $hh?->chronic_diseases ?: 'Tidak ada',
                'has_chronic_alert'       => $hh ? $this->hasClinicalNote($hh->chronic_diseases) : false,
                'current_medications'     => $hh?->current_medications ?: 'Tidak ada',
                'past_surgeries'          => $hh?->past_surgeries ?: 'Tidak ada',
                'emergency_contact_name'  => $hh?->emergency_contact_name ?? '-',
                'emergency_contact_phone' => $hh?->emergency_contact_phone ?? '-',
            ],
            'stations' => $stations,
        ];
    }

    protected function hasClinicalNote(?string $value): bool
    {
        if (!$value) {
            return false;
        }
        $normalized = strtolower(trim($value));
        return !in_array($normalized, ['', '-', 'tidak ada', 'tidak', 'none', 'nihil'], true);
    }

    protected function translateStatus(string $status): string
    {
        return match ($status) {
            'draft'                 => 'Draft Pengisian',
            'awaiting_payment'      => 'Menunggu Pembayaran',
            'awaiting_verification' => 'Menunggu Verifikasi Admin',
            'cleared'               => 'Lunas & Tiket Aktif',
            'checked_in'            => 'Sudah Check-in',
            'in_progress'           => 'Sedang Pemeriksaan',
            'completed'             => 'Selesai Skrining',
            default                 => ucfirst($status),
        };
    }
}

