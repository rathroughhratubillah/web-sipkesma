<?php

namespace Tests\Feature;

use App\Models\HealthHistory;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Registration;
// (StationResult removed — halaman hasil skrining dihapus)
use App\Models\TestSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCrudActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected TestSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'student']);

        $this->admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $this->admin->assignRole('admin');

        $this->student = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs.test@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $this->student->assignRole('student');

        $this->session = TestSession::create([
            'session_name' => 'Sesi Pagi Gelombang 1',
            'session_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'quota' => 50,
            'is_active' => true,
        ]);
    }

    public function test_admin_user_crud_operations(): void
    {
        $this->actingAs($this->admin);

        // 1. List
        $this->get(route('admin.users.index'))->assertStatus(200);

        // 2. Create Page
        $this->get(route('admin.users.create'))->assertStatus(200);

        // 3. Store User
        $response = $this->post(route('admin.users.store'), [
            'name' => 'User Baru Testing',
            'email' => 'userbaru@uinssc.ac.id',
            'password' => 'secret123',
            'role' => 'student',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'userbaru@uinssc.ac.id']);

        $created = User::where('email', 'userbaru@uinssc.ac.id')->first();

        // 4. Edit Page
        $this->get(route('admin.users.edit', $created))->assertStatus(200);

        // 5. Update User
        $updateResponse = $this->put(route('admin.users.update', $created), [
            'name' => 'User Baru Diperbarui',
            'email' => 'userbaru@uinssc.ac.id',
            'password' => '',
            'role' => 'student',
        ]);
        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['name' => 'User Baru Diperbarui']);

        // 6. Delete User
        $delResponse = $this->delete(route('admin.users.destroy', $created));
        $delResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $created->id]);

        // 7. Prevent self-deletion
        $selfDelResponse = $this->delete(route('admin.users.destroy', $this->admin));
        $selfDelResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_profile_crud_operations(): void
    {
        $this->actingAs($this->admin);

        $profile = Profile::create([
            'user_id' => $this->student->id,
            'nim' => '2408107999',
            'full_name' => 'Profil Siswa Test',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '081234567890',
            'gender' => 'L',
            'birth_date' => '2006-05-10',
            'address' => 'Jl. Sudirman No 1',
        ]);

        // 1. List
        $this->get(route('admin.profiles.index'))->assertStatus(200);

        // 2. Edit Page
        $this->get(route('admin.profiles.edit', $profile))->assertStatus(200);

        // 3. Update
        $response = $this->put(route('admin.profiles.update', $profile), [
            'nim' => '2408107999',
            'full_name' => 'Profil Siswa Test Updated',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '081299998888',
            'gender' => 'L',
            'birth_date' => '2006-05-10',
            'address' => 'Jl. Palembang Baru',
        ]);
        $response->assertRedirect(route('admin.profiles.index'));
        $this->assertDatabaseHas('profiles', ['full_name' => 'Profil Siswa Test Updated']);

        // 4. Delete
        $delResponse = $this->delete(route('admin.profiles.destroy', $profile));
        $delResponse->assertRedirect(route('admin.profiles.index'));
        $this->assertDatabaseMissing('profiles', ['id' => $profile->id]);
    }

    public function test_admin_registration_crud_operations(): void
    {
        $this->actingAs($this->admin);

        $reg = Registration::create([
            'user_id' => $this->student->id,
            'test_session_id' => $this->session->id,
            'status' => 'draft',
            'queue_code' => null,
        ]);

        // 1. List
        $this->get(route('admin.registrations.index'))->assertStatus(200);

        // 2. Edit Page
        $this->get(route('admin.registrations.edit', $reg))->assertStatus(200);

        // 3. Update
        $response = $this->put(route('admin.registrations.update', $reg), [
            'status' => 'cleared',
            'test_session_id' => $this->session->id,
            'queue_code' => 'A-099',
        ]);
        $response->assertRedirect(route('admin.registrations.index'));
        $this->assertDatabaseHas('registrations', [
            'id' => $reg->id,
            'status' => 'cleared',
            'queue_code' => 'A-099',
        ]);

        // 4. Delete
        $delResponse = $this->delete(route('admin.registrations.destroy', $reg));
        $delResponse->assertRedirect(route('admin.registrations.index'));
        $this->assertDatabaseMissing('registrations', ['id' => $reg->id]);
    }

    public function test_admin_payment_operations(): void
    {
        $this->actingAs($this->admin);

        $reg = Registration::create([
            'user_id' => $this->student->id,
            'test_session_id' => $this->session->id,
            'status' => 'awaiting_payment',
        ]);

        $payment = Payment::create([
            'registration_id' => $reg->id,
            'amount' => 150000,
            'proof_file_path' => 'uploads/proofs/sample.jpg',
            'status' => 'pending',
            'bank_name' => 'SeaBank',
            'sender_name' => 'Test Sender',
        ]);

        // 1. List
        $this->get(route('admin.payments.index'))->assertStatus(200);

        // 2. Update Status
        $response = $this->put(route('admin.payments.update', $payment), [
            'status' => 'verified',
        ]);
        $response->assertRedirect(route('admin.payments.index'));
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
        ]);
        $this->assertEquals('cleared', $reg->fresh()->status);
        $this->assertNotNull($reg->fresh()->queue_code);

        // 3. Delete Payment
        $delResponse = $this->delete(route('admin.payments.destroy', $payment));
        $delResponse->assertRedirect(route('admin.payments.index'));
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_admin_health_history_crud_operations(): void
    {
        $this->actingAs($this->admin);

        $profile = Profile::create([
            'user_id' => $this->student->id,
            'nim' => '2408107111',
            'full_name' => 'Mahasiswa Test Health',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '081234567890',
            'gender' => 'L',
            'birth_date' => '2006-05-10',
            'address' => 'Jl. Sudirman No 1',
        ]);

        $hh = HealthHistory::create([
            'user_id' => $this->student->id,
            'blood_type' => 'O',
            'rhesus' => '+',
            'height_cm' => 170,
            'weight_kg' => 65,
            'allergies' => 'Tidak ada',
            'chronic_diseases' => 'Tidak ada',
            'current_medications' => 'Tidak ada',
            'past_surgeries' => 'Tidak ada',
            'is_smoker' => false,
            'vaccine_status' => 'Lengkap Booster',
            'emergency_contact_name' => 'Bapak Test',
            'emergency_contact_phone' => '081299998888',
        ]);

        // 1. Edit Page
        $this->get(route('admin.health.edit', $this->student->id))->assertStatus(200);

        // 2. Update
        $response = $this->put(route('admin.health.update', $this->student->id), [
            'name' => 'Mahasiswa Test Health Updated',
            'full_name' => 'Mahasiswa Test Health Updated',
            'nim' => '2408107111',
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
            'phone' => '081234567890',
            'gender' => 'L',
            'birth_date' => '2006-05-10',
            'address' => 'Palembang',
            'blood_type' => 'B',
            'rhesus' => '+',
            'height_cm' => 175,
            'weight_kg' => 70,
            'allergies' => 'Dingin',
            'chronic_diseases' => 'Tidak ada',
            'current_medications' => 'Tidak ada',
            'past_surgeries' => 'Tidak ada',
            'is_smoker' => '0',
            'vaccine_status' => 'Booster 2',
            'emergency_contact_name' => 'Ibu Test',
            'emergency_contact_phone' => '081211112222',
        ]);
        $response->assertRedirect(route('admin.health.index', [
            'faculty' => 'Fakultas Syariah',
            'major' => 'Ilmu Falak',
        ]));
        $this->assertDatabaseHas('health_histories', [
            'user_id' => $this->student->id,
            'blood_type' => 'B',
            'allergies' => 'Dingin',
        ]);

        // 3. Delete
        $delResponse = $this->delete(route('admin.health.destroy', $this->student->id));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('health_histories', ['user_id' => $this->student->id]);
    }
}

