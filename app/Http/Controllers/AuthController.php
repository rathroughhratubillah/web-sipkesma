<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
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
        if ($user->hasRole('admin') || $user->hasRole('staff')) {
            return redirect()->route('staff.dashboard');
        }
        return redirect()->route('student.beranda');
    }
}
