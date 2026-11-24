<?php

namespace Tests\Feature;

use App\Models\HealthHistory;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Registration;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected TestSession $session;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'admin']);

        $this->student = User::create([
            'name' => 'Ahmad Fauzan',
            'email' => 'ahmad@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $this->student->assignRole('student');

        Registration::create([
            'user_id' => $this->student->id,
            'status' => 'draft',
        ]);

        $this->session = TestSession::create([
            'session_name' => 'Sesi 1 Pagi',
            'session_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'quota' => 40,
            'is_active' => true,
        ]);
    }

    public function test_student_navigation_and_beranda(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.beranda'));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzan');
        $response->assertSee('Tahapan Skrining');
        $response->assertSee('Langkah berikutnya');

        // Nav tabs present without certificate
        $response->assertSee('Beranda');
        $response->assertSee('Data Diri');
        $response->assertSee('Riwayat');
        $response->assertSee('Jadwal');
        $response->assertSee('Konfirmasi');
        $response->assertSee('Pembayaran');
        $response->assertSee('Tiket');
    }

    public function test_student_can_fill_and_submit_profile(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.profile'));
        $response->assertStatus(200);
        $response->assertSee('Formulir Data Diri Mahasiswa');
        $response->assertSee('Simpan & Lanjutkan', false);

        $postResponse = $this->actingAs($this->student)->post(route('student.profile.store'), [
            'nim' => '2026112233',
            'full_name' => 'Ahmad Fauzan Al-Ghifari',
            'faculty' => 'Fakultas Sains Dan Teknologi (FST)',
            'major' => 'Sistem Informasi',
            'gender' => 'L',
            'birth_date' => '2006-04-12',
            'phone' => '081234567890',
            'address' => 'Jl. Angkatan 45 No. 10 Palembang',
        ]);

        $postResponse->assertRedirect(route('student.health_history'));
        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->student->id,
            'nim' => '2026112233',
            'full_name' => 'Ahmad Fauzan Al-Ghifari',
        ]);
    }

    public function test_student_can_fill_and_submit_health_history(): void
    {
        // First set profile
        Profile::create([
            'user_id' => $this->student->id,
            'nim' => '2026112233',
            'full_name' => 'Ahmad Fauzan',
            'faculty' => 'Fakultas Sains Dan Teknologi (FST)',
            'major' => 'Sistem Informasi',
            'gender' => 'L',
            'birth_date' => '2006-04-12',
            'phone' => '081234567890',
            'address' => 'Jl. Palembang',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.health_history'));
        $response->assertStatus(200);
        $response->assertSee('Formulir Riwayat Kesehatan');
        $response->assertSee('Simpan & Pilih Jadwal', false);

        $postResponse = $this->actingAs($this->student)->post(route('student.health_history.store'), [
            'blood_type' => 'B',
            'rhesus' => '+',
            'height_cm' => 172.5,
            'weight_kg' => 64.0,
            'allergies' => 'Tidak ada',
            'chronic_diseases' => 'Tidak ada',
            'current_medications' => 'Tidak ada',
            'past_surgeries' => 'Tidak ada',
            'is_smoker' => 0,
            'vaccine_status' => 'Lengkap (Dosis 3/Booster)',
            'emergency_contact_name' => 'H. Usman',
            'emergency_contact_phone' => '081122334455',
        ]);

        $postResponse->assertRedirect(route('student.schedule'));
        $this->assertDatabaseHas('health_histories', [
            'user_id' => $this->student->id,
            'blood_type' => 'B',
            'height_cm' => 172.5,
        ]);
    }

    public function test_student_can_select_schedule_session(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.schedule'));
        $response->assertStatus(200);
        $response->assertSee('Pilih Jadwal Sesi Tes');
        $response->assertSee('Pilih & Tinjau Konfirmasi', false);

        $postResponse = $this->actingAs($this->student)->post(route('student.schedule.select'), [
            'test_session_id' => $this->session->id,
        ]);

        $postResponse->assertRedirect(route('student.confirmation'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->student->id,
            'test_session_id' => $this->session->id,
        ]);
    }

    public function test_student_can_confirm_and_lock_registration(): void
    {
        Profile::create([
            'user_id' => $this->student->id,
            'nim' => '2026112233',
            'full_name' => 'Ahmad Fauzan',
            'faculty' => 'FST',
            'major' => 'Sistem Informasi',
            'gender' => 'L',
            'birth_date' => '2006-04-12',
            'phone' => '081234567890',
            'address' => 'Palembang',
        ]);

        HealthHistory::create([
            'user_id' => $this->student->id,
            'blood_type' => 'B',
            'rhesus' => '+',
            'height_cm' => 170,
            'weight_kg' => 60,
            'is_smoker' => false,
            'vaccine_status' => 'Lengkap',
            'emergency_contact_name' => 'H. Usman',
            'emergency_contact_phone' => '081122334455',
        ]);

        $this->student->registration->update([
            'test_session_id' => $this->session->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.confirmation'));
        $response->assertStatus(200);
        $response->assertSee('Tinjau & Konfirmasi Data', false);
        $response->assertSee('Kunci Data & Lanjut Pembayaran', false);

        $lockResponse = $this->actingAs($this->student)->post(route('student.confirmation.lock'));
        $lockResponse->assertRedirect(route('student.payment'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->student->id,
            'status' => 'awaiting_payment',
        ]);
    }

    public function test_student_payment_page_ui_and_upload_and_verified_button(): void
    {
        Profile::create([
            'user_id' => $this->student->id,
            'nim' => '2026112233',
            'full_name' => 'Ahmad Fauzan',
            'faculty' => 'FST',
            'major' => 'Sistem Informasi',
            'gender' => 'L',
            'birth_date' => '2006-04-12',
            'phone' => '081234567890',
            'address' => 'Palembang',
        ]);

        $this->student->registration->update([
            'test_session_id' => $this->session->id,
            'status' => 'awaiting_payment',
        ]);

        // 1. Visit payment page before upload
        $response = $this->actingAs($this->student)->get(route('student.payment'));
        $response->assertStatus(200);
        $response->assertSee('Konfirmasi & Pembayaran', false);
        // Ensure stepper indicator is removed as requested
        $response->assertDontSee('<!-- Stepper Indicator -->', false);
        $response->assertDontSee('<span class="hidden sm:inline">Layanan</span>', false);

        // 2. Upload proof
        $file = UploadedFile::fake()->image('struk_seabank.jpg');
        $uploadResponse = $this->actingAs($this->student)->post(route('student.payment.store'), [
            'bank_name' => 'SeaBank',
            'sender_name' => 'Ahmad Fauzan',
            'sender_phone' => '081234567890',
            'proof_file' => $file,
        ]);

        $uploadResponse->assertRedirect(route('student.payment'));
        $this->assertDatabaseHas('payments', [
            'registration_id' => $this->student->registration->id,
            'bank_name' => 'SeaBank',
            'status' => 'pending',
        ]);

        // 3. Admin verifies payment and issues queue code
        $admin = User::create([
            'name' => 'Petugas Verifikasi',
            'email' => 'verifikator@uinssc.ac.id',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        $payment = Payment::where('registration_id', $this->student->registration->id)->first();
        $this->actingAs($admin)->post(route('staff.payments.approve', $payment->id));

        // 4. Student returns to payment page -> shows "Buka Antrean" button (no QR in text)
        $verifiedResponse = $this->actingAs($this->student)->get(route('student.payment'));
        $verifiedResponse->assertStatus(200);
        $verifiedResponse->assertSee('Pembayaran Telah Terverifikasi!');
        $verifiedResponse->assertSee('Buka Antrean');
        $verifiedResponse->assertDontSee('Buka Tiket Antrean & QR');

        // 5. Click "Buka Antrean" -> opens ticket
        $ticketResponse = $this->actingAs($this->student)->get(route('student.ticket'));
        $ticketResponse->assertStatus(200);
        $ticketResponse->assertSee('SIPKESMA UINSSC');
        $ticketResponse->assertSee('Nomor Antrean Anda');
        $ticketResponse->assertSee('Cetak Tiket Langsung');
        $ticketResponse->assertSee('Unduh PDF Resmi');

        // 6. Download ticket PDF
        $downloadResponse = $this->actingAs($this->student)->get(route('student.ticket.download'));
        $downloadResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $downloadResponse->headers->get('content-type'));
    }

    public function test_student_safe_redirects_and_logout(): void
    {
        // Accessing legacy certificate or status routes redirects directly to ticket
        $certResponse = $this->actingAs($this->student)->get(route('student.certificate'));
        $certResponse->assertRedirect(route('student.ticket'));

        $statusResponse = $this->actingAs($this->student)->get(route('student.status'));
        $statusResponse->assertRedirect(route('student.ticket'));

        // Logout
        $logoutResponse = $this->actingAs($this->student)->post(route('logout'));
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }
}
