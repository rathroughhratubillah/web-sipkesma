<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentAndCertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_student_payment_page_shows_seabank_and_dana_details(): void
    {
        $user = User::where('email', 'ratu@uinssc.ac.id')->first();
        $this->actingAs($user);

        $response = $this->get(route('student.payment'));
        $response->assertStatus(200);

        // Assert SeaBank and DANA presence
        $response->assertSee('901028036540');
        $response->assertSee('SeaBank');
        $response->assertSee('0882000260420');
        $response->assertSee('DANA');

        // Assert Data Diri & Queue / Name
        $response->assertSee('Data Diri Anda');
        $response->assertSee('Nomor Antrean');
        $response->assertSee('Ratu Ayu Maharani');
    }

    public function test_student_can_upload_payment_proof(): void
    {
        Storage::fake('public');
        $user = User::where('email', 'ratu@uinssc.ac.id')->first();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post(route('student.payment.store'), [
            'bank_name' => 'SeaBank',
            'sender_name' => 'Ratu Ayu Maharani',
            'sender_phone' => '081234567890',
            'proof_file' => $file,
        ]);

        // Upload mengarah kembali ke halaman pembayaran dengan status pending (menunggu verifikasi admin)
        $response->assertRedirect(route('student.payment'));
        $this->assertDatabaseHas('payments', [
            'registration_id' => $user->registration->id,
            'bank_name' => 'SeaBank',
            'status' => 'pending',
        ]);

        // Admin melakukan verifikasi/persetujuan pembayaran
        $admin = User::where('email', 'admin@uinssc.ac.id')->first();
        $payment = Payment::where('registration_id', $user->registration->id)->first();
        $this->actingAs($admin)->post(route('staff.payments.approve', $payment->id));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $user->registration->id,
            'status' => 'cleared',
        ]);
    }

    public function test_admin_and_staff_payments_show_seabank_and_dana(): void
    {
        $admin = User::where('email', 'admin@uinssc.ac.id')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.payments.index'));
        $response->assertStatus(200);
        $response->assertSee('SeaBank');
        $response->assertSee('DANA');

        $staff = User::where('email', 'petugas@uinssc.ac.id')->first();
        $this->actingAs($staff);

        $responseStaff = $this->get(route('staff.payments.index'));
        $responseStaff->assertStatus(200);
        $responseStaff->assertSee('SeaBank');
        $responseStaff->assertSee('DANA');
    }

    public function test_student_can_view_certificate_page_and_status(): void
    {
        $user = User::where('email', 'ratu@uinssc.ac.id')->first();
        $this->actingAs($user);

        // Akses tiket
        $responseTicket = $this->get(route('student.ticket'));
        $responseTicket->assertStatus(200);

        // Halaman sertifikat dan status lama dialihkan ke tiket
        $responseCert = $this->get(route('student.certificate'));
        $responseCert->assertRedirect(route('student.ticket'));

        $responseStatus = $this->get(route('student.status'));
        $responseStatus->assertRedirect(route('student.ticket'));
    }

    public function test_student_completed_can_download_certificate_and_verify(): void
    {
        $user = User::where('email', 'faris@uinssc.ac.id')->first();
        $this->actingAs($user);

        // Rute sertifikat mahasiswa dialihkan ke tiket
        $response = $this->get(route('student.certificate'));
        $response->assertRedirect(route('student.ticket'));

        $responseDownload = $this->get(route('student.certificate.download'));
        $responseDownload->assertRedirect(route('student.ticket'));

        // Public verification URL tetap valid untuk scan QR
        $certCode = 'SIPKESMA/SKK/' . date('Y') . '/' . str_pad($user->registration->id, 5, '0', STR_PAD_LEFT);
        $responseVerify = $this->get(route('certificate.verify', ['code' => $certCode]));
        $responseVerify->assertStatus(200);
        $responseVerify->assertSee('Surat Keterangan Kesehatan Terdaftar');
        $responseVerify->assertSee('Faris Al-Fatih');
    }
}
