<?php

namespace Tests\Feature;

use App\Models\HealthHistory;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Registration;
use App\Models\StationResult;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SipkesmaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_student_can_register_and_data_is_saved_in_database()
    {
        $response = $this->post('/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'maba@uinssc.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/beranda');

        $this->assertDatabaseHas('users', [
            'name' => 'Mahasiswa Baru',
            'email' => 'maba@uinssc.ac.id',
        ]);

        $user = User::where('email', 'maba@uinssc.ac.id')->first();
        $this->assertTrue($user->hasRole('student'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'status' => 'draft',
        ]);
    }

    public function test_student_can_fill_profile_and_health_and_schedule_and_payment()
    {
        $user = User::create([
            'name' => 'Testing Student',
            'email' => 'student@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('student');
        Registration::create(['user_id' => $user->id, 'status' => 'draft']);

        $session = TestSession::create([
            'session_name' => 'Sesi Pagi Test',
            'session_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'quota' => 50,
            'is_active' => true,
        ]);

        // 1. Data Diri
        $response = $this->actingAs($user)->post('/profil', [
            'nim' => '2026998877',
            'full_name' => 'Testing Student Full',
            'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
            'major' => 'Informatika',
            'phone' => '081234567890',
            'gender' => 'L',
            'birth_date' => '2006-05-10',
            'address' => 'Jl. Kampus UIN No. 1',
        ]);
        $response->assertRedirect(route('student.health_history'));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'nim' => '2026998877',
            'major' => 'Informatika',
        ]);

        // 2. Riwayat Kesehatan
        $response = $this->actingAs($user)->post('/riwayat-kesehatan', [
            'blood_type' => 'O',
            'rhesus' => '+',
            'height_cm' => 170,
            'weight_kg' => 60,
            'allergies' => 'Tidak ada',
            'chronic_diseases' => 'Tidak ada',
            'is_smoker' => 0,
            'vaccine_status' => 'Lengkap (Booster)',
            'emergency_contact_name' => 'Orang Tua',
            'emergency_contact_phone' => '081233445566',
        ]);
        $response->assertRedirect(route('student.schedule'));
        $this->assertDatabaseHas('health_histories', [
            'user_id' => $user->id,
            'blood_type' => 'O',
        ]);

        // 3. Pilih Jadwal
        $response = $this->actingAs($user)->post('/jadwal/pilih', [
            'test_session_id' => $session->id,
        ]);
        $response->assertRedirect(route('student.confirmation'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'test_session_id' => $session->id,
        ]);

        // 4. Konfirmasi
        $response = $this->actingAs($user)->post('/konfirmasi/kunci');
        $response->assertRedirect(route('student.payment'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'status' => 'awaiting_payment',
        ]);

        // 5. Pembayaran (Upload bukti transfer -> menunggu verifikasi admin)
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $response = $this->actingAs($user)->post('/pembayaran/upload', [
            'proof_file' => $file,
            'bank_name' => 'Bank Mandiri',
            'sender_name' => 'Testing Student',
        ]);
        $response->assertRedirect(route('student.payment'));
        $this->assertDatabaseHas('payments', [
            'bank_name' => 'Bank Mandiri',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'status' => 'awaiting_payment',
        ]);

        // Verifikasi & Persetujuan Pembayaran oleh Admin
        $adminForApproval = User::create([
            'name' => 'Admin Approval User',
            'email' => 'admin_approval@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $adminForApproval->assignRole('admin');

        $payment = Payment::where('bank_name', 'Bank Mandiri')->first();
        $this->actingAs($adminForApproval)->post(route('staff.payments.approve', $payment->id));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
        ]);
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'status' => 'cleared',
        ]);
    }

    public function test_admin_can_manage_all_data_in_database()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Admin creates user
        $response = $this->actingAs($admin)->post('/admin/pengguna', [
            'name' => 'Baru Dari Admin',
            'email' => 'baru@uinssc.ac.id',
            'password' => 'password123',
            'role' => 'student',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'baru@uinssc.ac.id']);

        $createdUser = User::where('email', 'baru@uinssc.ac.id')->first();

        // Admin creates / edits profile
        $profile = Profile::create([
            'user_id' => $createdUser->id,
            'nim' => '2026112233',
            'full_name' => 'Baru Dari Admin',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '089988776655',
            'gender' => 'L',
            'birth_date' => '2006-01-01',
            'address' => 'Palembang',
        ]);

        $response = $this->actingAs($admin)->put("/admin/profil/{$profile->id}", [
            'nim' => '2026112233',
            'full_name' => 'Baru Dari Admin (Updated)',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '089988776655',
            'gender' => 'L',
            'birth_date' => '2006-01-01',
            'address' => 'Palembang Baru',
        ]);
        $response->assertRedirect(route('admin.profiles.index'));
        $this->assertDatabaseHas('profiles', ['full_name' => 'Baru Dari Admin (Updated)']);

        // Admin verifies payment
        $reg = $createdUser->registration;
        $reg->update(['status' => 'awaiting_verification']);
        $payment = Payment::create([
            'registration_id' => $reg->id,
            'amount' => 150000,
            'proof_file_path' => 'uploads/proofs/sample.jpg',
            'status' => 'pending',
            'bank_name' => 'Bank BSI',
            'sender_name' => 'Baru Dari Admin',
        ]);

        $response = $this->actingAs($admin)->put("/admin/pembayaran/{$payment->id}/status", [
            'status' => 'verified',
        ]);
        $response->assertRedirect(route('admin.payments.index'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
        ]);
        $reg->refresh();
        $this->assertEquals('cleared', $reg->status);
        $this->assertNotNull($reg->queue_code);
    }

    public function test_single_admin_role_can_access_both_admin_and_operational_medical_portal()
    {
        $admin = User::create([
            'name' => 'Koordinator Layanan',
            'email' => 'koordinator@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Test login redirect to admin dashboard
        $loginResponse = $this->post('/login', [
            'email' => 'koordinator@uinssc.ac.id',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect(route('admin.dashboard'));

        // Admin can access admin panel & see progress trend chart and recap guidance banner
        $dashboardResponse = $this->actingAs($admin)->get('/admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Tren Progres (7 Hari Terakhir)');
        $dashboardResponse->assertSee('Rekap Peserta PPKMB');
        $dashboardResponse->assertSee('Rekap Riwayat Kesehatan');

        // Admin can access all operational medical features in unified panel
        $this->actingAs($admin)->get('/petugas')->assertStatus(200);
        $this->actingAs($admin)->get('/petugas/pembayaran')->assertStatus(200);
        $this->actingAs($admin)->get('/petugas/checkin')->assertStatus(200);
        $this->actingAs($admin)->get('/petugas/antrian')->assertRedirect(route('staff.station.index', 'urin'));
        $this->actingAs($admin)->get('/petugas/stasiun/urin')->assertStatus(200);
        $this->actingAs($admin)->get('/petugas/stasiun/napza')->assertStatus(200);
        $this->actingAs($admin)->get('/petugas/stasiun/pemeriksaan')->assertStatus(200);
        // Test hierarchical participant breakdown (Halaman dipisah: Fakultas -> Jurusan -> Tabel Mahasiswa)
        $facResponse = $this->actingAs($admin)->get('/petugas/peserta');
        $facResponse->assertStatus(200);
        $facResponse->assertSee('Daftar Fakultas (Pilih Salah Satu)');

        $majorListResponse = $this->actingAs($admin)->get('/petugas/peserta?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)');
        $majorListResponse->assertStatus(200);
        $majorListResponse->assertSee('Pilih Program Studi / Jurusan');
        $majorListResponse->assertSee('Informatika');

        $majResponse = $this->actingAs($admin)->get('/petugas/peserta?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)&major=Informatika');
        $majResponse->assertStatus(200);
        $majResponse->assertSee('Tabel Mahasiswa — Program Studi');
        $majResponse->assertSee('Informatika');

        // Test export CSV for specific major
        $exportResponse = $this->actingAs($admin)->get('/petugas/peserta/export?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)&major=Informatika');
        $exportResponse->assertStatus(200);

        // Test hierarchical health history breakdown (Halaman dipisah: Fakultas -> Jurusan -> Tabel Riwayat Kesehatan)
        $healthFacResponse = $this->actingAs($admin)->get('/admin/kesehatan');
        $healthFacResponse->assertStatus(200);
        $healthFacResponse->assertSee('Kelola Riwayat Kesehatan Mahasiswa');
        $healthFacResponse->assertSee('Daftar Fakultas (Pilih Salah Satu)');

        $healthMajListResponse = $this->actingAs($admin)->get('/admin/kesehatan?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)');
        $healthMajListResponse->assertStatus(200);
        $healthMajListResponse->assertSee('Pilih Program Studi / Jurusan');
        $healthMajListResponse->assertSee('Informatika');

        $healthTableResponse = $this->actingAs($admin)->get('/admin/kesehatan?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)&major=Informatika');
        $healthTableResponse->assertStatus(200);
        $healthTableResponse->assertSee('Tabel Riwayat Kesehatan');
        $healthTableResponse->assertSee('Informatika');

        // Test export CSV for health history
        $healthExportResponse = $this->actingAs($admin)->get('/admin/kesehatan/export?faculty=Fakultas+Ilmu+Tarbiyah+Dan+Keguruan+(FITK)&major=Informatika');
        $healthExportResponse->assertStatus(200);

        // Test edit health form for student
        $testStudent = User::role('student')->first();
        if ($testStudent) {
            $this->actingAs($admin)->get('/admin/kesehatan/' . $testStudent->id . '/edit')->assertStatus(200);
        }

        // Verify core roles exist in system (dokter role may also exist)
        $roles = Role::pluck('name')->sort()->values()->toArray();
        $this->assertContains('admin', $roles);
        $this->assertContains('student', $roles);
    }
}
