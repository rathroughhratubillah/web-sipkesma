<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Registration;
use App\Services\CampusApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($validated['email']);
        $password = $validated['password'];

        // 1. Integrasi API Portal Kampus v2 (NIP Pegawai / Username Admin / Mahasiswa)
        $campusApi = new CampusApiService();
        $campusAuth = $campusApi->login($loginInput, $password);

        if ($campusAuth && !empty($campusAuth['username'])) {
            $identifier = $campusAuth['username'];
            $roleName = strtolower($campusAuth['role'] ?? 'student');
            $isAdminOrStaff = in_array($roleName, ['admin', 'pegawai', 'staff', 'staf', 'dosen']);

            $token = $campusAuth['token'] ?? null;
            $campusProfile = $token ? $campusApi->getProfile($token) : null;

            $email = $campusAuth['email'] ?? ($campusProfile['email'] ?? ($identifier . '@uinssc.ac.id'));
            $fullName = $campusProfile['nama'] ?? ($campusAuth['nama'] ?? $identifier);

            $user = User::where('email', $email)
                ->orWhereHas('profile', fn($q) => $q->where('nim', $identifier))
                ->first();

            $targetRole = $isAdminOrStaff ? 'admin' : 'student';

            if (!$user) {
                $user = User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);
                $role = Role::firstOrCreate(['name' => $targetRole]);
                $user->assignRole($role);
            } else {
                $user->update(['password' => Hash::make($password)]);
                if (!$user->hasRole($targetRole)) {
                    $user->assignRole(Role::firstOrCreate(['name' => $targetRole]));
                }
            }

            // Jika bukan admin/staf (mahasiswa), kelola data profil dan pendaftaran
            if (!$isAdminOrStaff) {
                Profile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nim'        => $identifier,
                        'full_name'  => $fullName,
                        'faculty'    => $campusProfile['fakultas_nama'] ?? ($user->profile?->faculty ?? 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)'),
                        'major'      => $campusProfile['program_studi_nama'] ?? ($user->profile?->major ?? 'Tadris Matematika'),
                        'phone'      => $campusProfile['no_hp'] ?? ($user->profile?->phone ?? '08123456789'),
                        'gender'     => $campusProfile['jenis_kelamin'] ?? ($user->profile?->gender ?? 'L'),
                        'birth_date' => $campusProfile['tanggal_lahir'] ?? ($user->profile?->birth_date ?? '2005-02-25'),
                        'address'    => $campusProfile['alamat'] ?? ($user->profile?->address ?? 'Alamat Mahasiswa'),
                    ]
                );

                Registration::firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'draft']
                );
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $welcomeMsg = $isAdminOrStaff
                ? "Selamat datang, {$fullName}! Berhasil masuk sebagai Administrator/Petugas Kampus melalui API Portal."
                : "Selamat datang, {$fullName}! Berhasil masuk menggunakan akun Portal Kampus.";

            return $this->redirectBasedOnRole($user)->with('success', $welcomeMsg);
        }

        // 2. Fallback Autentikasi Database Lokal Sipkesma
        // Cari user berdasarkan email (case-insensitive) atau nama
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($loginInput)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($loginInput)])
            ->orWhereHas('profile', fn($q) => $q->where('nim', $loginInput))
            ->first();

        // 2. Alias khusus admin (admin, administrator, admin@gmail.com, admin@uinssc.ac.id)
        if (!$user && in_array(strtolower($loginInput), ['admin', 'administrator', 'admin@gmail.com', 'admin@uinssc.ac.id'])) {
            $user = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->orWhere('email', 'admin@uinssc.ac.id')
                ->first();
        }

        // 3. Alias akun admin medis / dr. Nurul (petugas, petugas@uinssc.ac.id, petugas@gmail.com)
        if (!$user && in_array(strtolower($loginInput), ['petugas', 'petugas@uinssc.ac.id', 'petugas@gmail.com'])) {
            $user = User::where('email', 'petugas@uinssc.ac.id')->first();
        }

        // 4. Verifikasi kecocokan password
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email/Nama atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $user->assignRole($studentRole);

        Registration::create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        Auth::login($user);

        return redirect()->route('student.beranda')->with('success', 'Akun berhasil dibuat! Selamat datang di Sipkesma.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Anda telah keluar dari sistem.');
    }

    // ───────────────────────────────────────────────
    // Google OAuth (Laravel Socialite)
    // ───────────────────────────────────────────────

    /**
     * Redirect user to Google's OAuth page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     * - If user exists by google_id  → login langsung
     * - If user exists by email      → link google_id dan login
     * - Otherwise                    → buat akun baru, assign student, buat registration
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        // 1. Cari berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            // 2. Cari berdasarkan email (akun sudah ada via password)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link google_id ke akun yang sudah ada
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                // 3. Buat akun baru untuk pengguna Google baru
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => null,
                ]);

                // Assign role student
                $studentRole = Role::firstOrCreate(['name' => 'student']);
                $user->assignRole($studentRole);

                // Buat entri registrasi awal
                Registration::create([
                    'user_id' => $user->id,
                    'status'  => 'draft',
                ]);
            }
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        $isNew = $user->wasRecentlyCreated;
        $message = $isNew
            ? 'Akun baru berhasil dibuat via Google! Selamat datang, ' . $user->name . '.'
            : 'Selamat datang kembali, ' . $user->name . '!';

        return $this->redirectBasedOnRole($user)->with('success', $message);
    }

    public function demoLogin(string $target)
    {
        $user = null;
        if ($target === 'ratu' || $target === 'student') {
            $user = User::where('email', 'ratu@uinssc.ac.id')->first();
        } elseif ($target === 'budi' || $target === 'student-cleared') {
            $user = User::where('email', 'budi@uinssc.ac.id')->first();
        } elseif ($target === 'siti' || $target === 'student-completed') {
            $user = User::where('email', 'siti@uinssc.ac.id')->first();
        } elseif ($target === 'ahmad') {
            $user = User::where('email', 'ahmad@uinssc.ac.id')->first();
        } elseif ($target === 'staff' || $target === 'petugas') {
            $user = User::where('email', 'petugas@uinssc.ac.id')->first();
        } elseif ($target === 'admin') {
            $user = User::where('email', 'admin@uinssc.ac.id')->first();
        } else {
            $user = User::where('email', $target . '@uinssc.ac.id')->first();
        }

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirectBasedOnRole($user)->with('success', "Beralih akun ke: {$user->name} ({$user->roles->first()?->name})");
        }

        return redirect('/login')->with('error', 'Akun demo tidak ditemukan.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.beranda');
    }
}
