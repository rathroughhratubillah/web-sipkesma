<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Staff\CheckInController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\ParticipantController;
use App\Http\Controllers\Staff\PaymentVerificationController;
use App\Http\Controllers\Staff\QueueDisplayController;
use App\Http\Controllers\Staff\StationController;
use App\Http\Controllers\Staff\TestSessionController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\ConfirmationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\HealthHistoryController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ScheduleController;
use App\Http\Controllers\Student\StatusTrackerController;
use App\Http\Controllers\Student\TicketController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Certificate QR Verification
Route::get('/verifikasi-sertifikat', [CertificateVerificationController::class, 'verify'])->name('certificate.verify');

// Authentication & Demo Switcher
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{target}', [AuthController::class, 'demoLogin'])->name('demo.login');

// Student Portal (Role: student)
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/beranda', [StudentDashboardController::class, 'index'])->name('student.beranda');
    
    // 1. Data Diri
    Route::get('/profil', [ProfileController::class, 'index'])->name('student.profile');
    Route::post('/profil', [ProfileController::class, 'store'])->name('student.profile.store');

    // 2. Riwayat Kesehatan
    Route::get('/riwayat-kesehatan', [HealthHistoryController::class, 'index'])->name('student.health_history');
    Route::post('/riwayat-kesehatan', [HealthHistoryController::class, 'store'])->name('student.health_history.store');

    // 3. Pilih Jadwal
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('student.schedule');
    Route::post('/jadwal/pilih', [ScheduleController::class, 'select'])->name('student.schedule.select');

    // 4. Konfirmasi Pendaftaran
    Route::get('/konfirmasi', [ConfirmationController::class, 'index'])->name('student.confirmation');
    Route::post('/konfirmasi/kunci', [ConfirmationController::class, 'lock'])->name('student.confirmation.lock');

    // 5. Pembayaran
    Route::get('/pembayaran', [StudentPaymentController::class, 'index'])->name('student.payment');
    Route::post('/pembayaran/upload', [StudentPaymentController::class, 'store'])->name('student.payment.store');

    // 6. Tiket Antrean & QR
    Route::get('/tiket', [TicketController::class, 'index'])->name('student.ticket');
    Route::get('/tiket/download', [TicketController::class, 'downloadPdf'])->name('student.ticket.download');

    // 7. Status Pemeriksaan
    Route::get('/status', [StatusTrackerController::class, 'index'])->name('student.status');

    // 8. Sertifikat Kesehatan
    Route::get('/sertifikat', [CertificateController::class, 'index'])->name('student.certificate');
    Route::get('/sertifikat/download', [CertificateController::class, 'download'])->name('student.certificate.download');
});

// Staff & Admin Portal (Roles: staff, admin)
Route::middleware(['auth', 'role:staff|admin'])->prefix('petugas')->as('staff.')->group(function () {
    // 1. Dashboard Ringkasan
    Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');

    // 2. Verifikasi Pembayaran
    Route::get('/pembayaran', [PaymentVerificationController::class, 'index'])->name('payments.index');
    Route::post('/pembayaran/{payment}/approve', [PaymentVerificationController::class, 'approve'])->name('payments.approve');
    Route::post('/pembayaran/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');

    // 3. Meja Check-in & Scanner
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::post('/checkin/process', [CheckInController::class, 'process'])->name('checkin.process');

    // 4. Panggilan Antrean
    Route::get('/antrian', [QueueDisplayController::class, 'index'])->name('queue.display');
    Route::post('/antrian/{registration}/call', [QueueDisplayController::class, 'call'])->name('queue.call');

    // 5. Input Hasil 3 Stasiun
    Route::get('/stasiun/{station_name}', [StationController::class, 'index'])->name('station.index');
    Route::post('/stasiun/{station_name}/{registration}', [StationController::class, 'storeResult'])->name('station.store');

    // 6. Rekap Data Peserta
    Route::get('/peserta', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/peserta/export', [ParticipantController::class, 'exportCsv'])->name('participants.export');

    // 7. Kelola Kuota Jadwal
    Route::get('/jadwal', [TestSessionController::class, 'index'])->name('sessions.index');
    Route::post('/jadwal', [TestSessionController::class, 'store'])->name('sessions.store');
    Route::put('/jadwal/{session}', [TestSessionController::class, 'update'])->name('sessions.update');
    Route::delete('/jadwal/{session}', [TestSessionController::class, 'destroy'])->name('sessions.destroy');
});

// ============================================================
// Admin Panel (Role: admin only)
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Kelola Pengguna
    Route::get('/pengguna', [AdminController::class, 'users'])->name('users.index');
    Route::get('/pengguna/tambah', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/pengguna', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/pengguna/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/pengguna/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/pengguna/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Kelola Profil Mahasiswa
    Route::get('/profil', [AdminController::class, 'profiles'])->name('profiles.index');
    Route::get('/profil/{profile}/edit', [AdminController::class, 'editProfile'])->name('profiles.edit');
    Route::put('/profil/{profile}', [AdminController::class, 'updateProfile'])->name('profiles.update');
    Route::delete('/profil/{profile}', [AdminController::class, 'destroyProfile'])->name('profiles.destroy');

    // Kelola Riwayat Kesehatan
    Route::get('/kesehatan', [AdminController::class, 'healthHistories'])->name('health.index');
    Route::get('/kesehatan/{healthHistory}/edit', [AdminController::class, 'editHealth'])->name('health.edit');
    Route::put('/kesehatan/{healthHistory}', [AdminController::class, 'updateHealth'])->name('health.update');
    Route::delete('/kesehatan/{healthHistory}', [AdminController::class, 'destroyHealth'])->name('health.destroy');

    // Kelola Registrasi
    Route::get('/registrasi', [AdminController::class, 'registrations'])->name('registrations.index');
    Route::get('/registrasi/{registration}/edit', [AdminController::class, 'editRegistration'])->name('registrations.edit');
    Route::put('/registrasi/{registration}', [AdminController::class, 'updateRegistration'])->name('registrations.update');
    Route::delete('/registrasi/{registration}', [AdminController::class, 'destroyRegistration'])->name('registrations.destroy');

    // Kelola Pembayaran
    Route::get('/pembayaran', [AdminController::class, 'payments'])->name('payments.index');
    Route::put('/pembayaran/{payment}/status', [AdminController::class, 'updatePaymentStatus'])->name('payments.update');
    Route::delete('/pembayaran/{payment}', [AdminController::class, 'destroyPayment'])->name('payments.destroy');

    // Kelola Hasil Pemeriksaan / Stasiun Medis
    Route::get('/hasil-skrining', [AdminController::class, 'results'])->name('results.index');
    Route::get('/hasil-skrining/{stationResult}/edit', [AdminController::class, 'editResult'])->name('results.edit');
    Route::put('/hasil-skrining/{stationResult}', [AdminController::class, 'updateResult'])->name('results.update');
    Route::delete('/hasil-skrining/{stationResult}', [AdminController::class, 'destroyResult'])->name('results.destroy');
});

