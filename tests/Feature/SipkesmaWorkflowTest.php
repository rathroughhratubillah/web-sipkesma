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
        Role::firstOrCreate(['name' => 'staff']);
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

        // 5. Pembayaran
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
            'status' => 'awaiting_verification',
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
}
