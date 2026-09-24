<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthHistory;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Registration;
use App\Models\StationResult;
use App\Models\TestSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_profiles'      => Profile::count(),
            'total_health'        => HealthHistory::count(),
            'total_registrations' => Registration::count(),
            'total_payments'      => Payment::count(),
            'pending_payments'    => Payment::where('status', 'pending')->count(),
            'total_results'       => StationResult::count(),
            'completed'           => Registration::where('status', 'completed')->count(),
        ];

        $recentUsers = User::with(['profile', 'registration', 'roles'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    // ============================================================
    // KELOLA PENGGUNA (USERS)
    // ============================================================
    public function users(Request $request)
    {
        $search = $request->get('search');
        $role   = $request->get('role');

        $query = User::with(['profile', 'roles', 'registration']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'search', 'role', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        if ($validated['role'] === 'student') {
            Registration::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'draft']
            );
        }

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna '{$user->name}' berhasil dibuat!");
    }

    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            ...(isset($validated['password']) && $validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    // ============================================================
    // KELOLA PROFIL MAHASISWA
    // ============================================================
    public function profiles(Request $request)
    {
        $search  = $request->get('search');
        $faculty = $request->get('faculty');

        $query = Profile::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($faculty && $faculty !== 'all') {
            $query->where('faculty', $faculty);
        }

        $profiles = $query->latest()->paginate(20)->withQueryString();

        $faculties = [
            'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
            'Fakultas Ekonomi Dan Bisnis Islam (FEBI)',
            'Fakultas Syariah',
            'Fakultas Dakwah Dan Komunikasi Islam (FDKI)',
            'Fakultas Ushuluddin Dan Arab',
        ];

        return view('admin.profiles.index', compact('profiles', 'search', 'faculty', 'faculties'));
    }

    public function editProfile(Profile $profile)
    {
        $faculties = [
            'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)' => [
                'Informatika','Manajemen Pendidikan Islam','Matematika','Pendidikan Agama Islam',
                'Pendidikan Bahasa Arab','Pendidikan Guru Madrasah Ibtidaiyah','Pendidikan Islam Anak Usia Dini',
                'PJJ Pendidikan Agama Islam','PJJ Pendidikan Bahasa Arab','PJJ Pendidikan Guru Madrasah Ibtidaiyah',
                'Tadris Bahasa Indonesia','Tadris Bahasa Inggris','Tadris Biologi','Tadris Ilmu Pengetahuan Sosial',
                'Tadris Kimia','Tadris Matematika',
            ],
            'Fakultas Ekonomi Dan Bisnis Islam (FEBI)' => [
                'Akuntansi Syariah','Bioteknologi','Ekonomi Syariah','Pariwisata Syariah','Perbankan Syariah',
            ],
            'Fakultas Syariah' => [
                "Hukum Ekonomi Syari'ah (Muamalah)",'Hukum Keluarga (Akhwalul Syaksiyah)',
                'Hukum Tatanegara Islam','Ilmu Falak','PJJ Hukum Keluarga',
            ],
            'Fakultas Dakwah Dan Komunikasi Islam (FDKI)' => [
                'Bimbingan dan Konseling Islam','Komunikasi dan Penyiaran Islam',
                'Pengembangan Masyarakat Islam','Sosiologi Agama',
            ],
            'Fakultas Ushuluddin Dan Arab' => [
                'Aqidah dan Filsafat Islam','Bahasa dan Sastra Arab',"Ilmu Al-Qur'an dan Tafsir",
                'Ilmu Hadis','PJJ Sejarah Peradaban Islam','Sejarah Peradaban Islam','Tasawuf dan Psikoterapi',
            ],
        ];

        return view('admin.profiles.edit', compact('profile', 'faculties'));
    }

    public function updateProfile(Request $request, Profile $profile)
    {
        $validated = $request->validate([
            'nim'        => ['required', 'string', 'max:30', 'unique:profiles,nim,' . $profile->id],
            'full_name'  => ['required', 'string', 'max:255'],
            'faculty'    => ['required', 'string', 'max:255'],
            'major'      => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'gender'     => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date'],
            'address'    => ['required', 'string', 'max:1000'],
        ]);

        $profile->update($validated);

        return redirect()->route('admin.profiles.index')->with('success', 'Data profil berhasil diperbarui.');
    }

    public function destroyProfile(Profile $profile)
    {
        $profile->delete();
        return redirect()->route('admin.profiles.index')->with('success', 'Profil berhasil dihapus.');
    }

    // ============================================================
    // KELOLA RIWAYAT KESEHATAN
    // ============================================================
    public function healthHistories(Request $request)
    {
        $search = $request->get('search');

        $query = HealthHistory::with('user.profile');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('user.profile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $healthHistories = $query->latest()->paginate(20)->withQueryString();

        return view('admin.health.index', compact('healthHistories', 'search'));
    }

    public function editHealth(HealthHistory $healthHistory)
    {
        return view('admin.health.edit', compact('healthHistory'));
    }

    public function updateHealth(Request $request, HealthHistory $healthHistory)
    {
        $validated = $request->validate([
            'blood_type'              => ['required', 'string', 'in:A,B,AB,O,Tidak Tahu'],
            'rhesus'                  => ['required', 'string', 'in:+,-,Tidak Tahu'],
            'height_cm'               => ['required', 'numeric', 'min:50', 'max:250'],
            'weight_kg'               => ['required', 'numeric', 'min:20', 'max:300'],
            'allergies'               => ['nullable', 'string', 'max:500'],
            'chronic_diseases'        => ['nullable', 'string', 'max:500'],
            'current_medications'     => ['nullable', 'string', 'max:500'],
            'past_surgeries'          => ['nullable', 'string', 'max:500'],
            'is_smoker'               => ['required', 'boolean'],
            'vaccine_status'          => ['required', 'string', 'max:100'],
            'emergency_contact_name'  => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:30'],
        ]);

        $healthHistory->update($validated);

        return redirect()->route('admin.health.index')->with('success', 'Riwayat kesehatan berhasil diperbarui.');
    }

    public function destroyHealth(HealthHistory $healthHistory)
    {
        $healthHistory->delete();
        return redirect()->route('admin.health.index')->with('success', 'Riwayat kesehatan berhasil dihapus.');
    }

    // ============================================================
    // KELOLA REGISTRASI
    // ============================================================
    public function registrations(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Registration::with(['user.profile', 'testSession', 'payment']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('queue_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.profile', fn($pq) => $pq->where('full_name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $registrations = $query->latest()->paginate(20)->withQueryString();
        $sessions      = TestSession::orderBy('session_date')->get();

        return view('admin.registrations.index', compact('registrations', 'search', 'status', 'sessions'));
    }

    public function editRegistration(Registration $registration)
    {
        $sessions = TestSession::orderBy('session_date')->get();
        $statuses = [
            'draft', 'awaiting_payment', 'awaiting_verification',
            'cleared', 'checked_in', 'in_progress', 'completed',
        ];
        return view('admin.registrations.edit', compact('registration', 'sessions', 'statuses'));
    }

    public function updateRegistration(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'status'          => ['required', 'in:draft,awaiting_payment,awaiting_verification,cleared,checked_in,in_progress,completed'],
            'test_session_id' => ['nullable', 'exists:test_sessions,id'],
            'queue_code'      => ['nullable', 'string', 'max:50'],
        ]);

        $registration->update($validated);

        return redirect()->route('admin.registrations.index')->with('success', 'Data registrasi berhasil diperbarui.');
    }

    public function destroyRegistration(Registration $registration)
    {
        $registration->delete();
        return redirect()->route('admin.registrations.index')->with('success', 'Registrasi berhasil dihapus.');
    }

    // ============================================================
    // KELOLA PEMBAYARAN
    // ============================================================
    public function payments(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Payment::with(['registration.user.profile', 'registration.testSession', 'verifier']);

        if ($search) {
            $query->whereHas('registration.user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('registration.user.profile', fn($q) => $q->where('full_name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments', 'search', 'status'));
    }

    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status'           => ['required', 'in:pending,verified,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $data = ['status' => $validated['status']];
            if (isset($validated['rejection_reason'])) {
                $data['rejection_reason'] = $validated['rejection_reason'];
            }
            if ($validated['status'] === 'verified') {
                $data['verified_at'] = Carbon::now();
                $data['verified_by'] = auth()->id();
                $data['rejection_reason'] = null;
            }
            $payment->update($data);

            $registration = $payment->registration()->lockForUpdate()->first();
            if ($registration) {
                if ($validated['status'] === 'verified') {
                    // Generate queue code if not yet generated
                    if (!$registration->queue_code) {
                        $session = $registration->testSession;
                        $existingQueueCodes = Registration::where('test_session_id', $session?->id)
                            ->whereNotNull('queue_code')
                            ->pluck('queue_code')
                            ->toArray();

                        $maxNumber = 0;
                        foreach ($existingQueueCodes as $code) {
                            if (preg_match('/^[A-Z]-(\d+)$/', $code, $matches)) {
                                $num = (int) $matches[1];
                                if ($num > $maxNumber) {
                                    $maxNumber = $num;
                                }
                            }
                        }
                        $queueCode = 'A-' . str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
                        $registration->update([
                            'status' => 'cleared',
                            'queue_code' => $queueCode,
                        ]);
                    } else {
                        $registration->update(['status' => 'cleared']);
                    }
                } elseif ($validated['status'] === 'rejected') {
                    $registration->update(['status' => 'awaiting_payment']);
                }
            }
        });

        return redirect()->route('admin.payments.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function destroyPayment(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }

    // ============================================================
    // KELOLA HASIL PEMERIKSAAN / STASIUN MEDIS
    // ============================================================
    public function results(Request $request)
    {
        $search = $request->get('search');
        $station = $request->get('station');
        $status = $request->get('status');

        $query = StationResult::with(['registration.user.profile', 'registration.testSession']);

        if ($search) {
            $query->whereHas('registration.user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('registration.user.profile', fn($q) => $q->where('full_name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"))
                  ->orWhereHas('registration', fn($q) => $q->where('queue_code', 'like', "%{$search}%"));
        }

        if ($station && $station !== 'all') {
            $query->where('station_name', $station);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $results = $query->latest('examined_at')->paginate(20)->withQueryString();

        return view('admin.results.index', compact('results', 'search', 'station', 'status'));
    }

    public function editResult(StationResult $stationResult)
    {
        return view('admin.results.edit', compact('stationResult'));
    }

    public function updateResult(Request $request, StationResult $stationResult)
    {
        $validated = $request->validate([
            'status'        => ['required', 'in:pass,followup,fail'],
            'examiner_name' => ['required', 'string', 'max:255'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $stationResult->update($validated);

        // Check if registration should be marked as completed
        $registration = $stationResult->registration;
        if ($registration) {
            if ($registration->fresh(['stationResults'])->isAllStationsPassed()) {
                $registration->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }
        }

        return redirect()->route('admin.results.index')->with('success', 'Hasil pemeriksaan berhasil diperbarui.');
    }

    public function destroyResult(StationResult $stationResult)
    {
        $stationResult->delete();
        return redirect()->route('admin.results.index')->with('success', 'Data hasil pemeriksaan berhasil dihapus.');
    }
}
