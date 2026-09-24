<?php

namespace Database\Seeders;

use App\Models\HealthHistory;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Registration;
use App\Models\StationResult;
use App\Models\TestSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@uinssc.ac.id'],
            [
                'name' => 'Administrator PPKMB',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 3. Staff User
        $staff = User::firstOrCreate(
            ['email' => 'petugas@uinssc.ac.id'],
            [
                'name' => 'dr. Nurul Aini',
                'password' => Hash::make('password'),
            ]
        );
        $staff->syncRoles([$staffRole]);

        // 4. Test Sessions
        $session1 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Pagi Gelombang 1 (08:00 - 10:00)'],
            [
                'session_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'quota' => 100,
                'is_active' => true,
            ]
        );

        $session2 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Siang Gelombang 1 (10:30 - 12:30)'],
            [
                'session_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'start_time' => '10:30:00',
                'end_time' => '12:30:00',
                'quota' => 100,
                'is_active' => true,
            ]
        );

        $session3 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Pagi Gelombang 2 (08:00 - 10:00)'],
            [
                'session_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'quota' => 120,
                'is_active' => true,
            ]
        );

        // 5. Student 1: Ratu (matches Mockup Image 5: Welcome Ratu, data diri ready, test session chosen)
        $ratu = User::firstOrCreate(
            ['email' => 'ratu@uinssc.ac.id'],
            [
                'name' => 'Ratu',
                'password' => Hash::make('password'),
            ]
        );
        $ratu->syncRoles([$studentRole]);

        Profile::updateOrCreate(
            ['user_id' => $ratu->id],
            [
                'nim' => '2026101001',
                'full_name' => 'Ratu Ayu Maharani',
                'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'major' => 'Informatika',
                'phone' => '081234567890',
                'gender' => 'P',
                'birth_date' => '2007-04-15',
                'address' => 'Jl. Merdeka No. 12, Kota Palembang',
            ]
        );

        // Ratu has selected schedule, status draft
        Registration::updateOrCreate(
            ['user_id' => $ratu->id],
            [
                'test_session_id' => $session1->id,
                'status' => 'draft',
                'queue_code' => null,
            ]
        );

        // 6. Student 2: Budi (Cleared & has Ticket A-001)
        $budi = User::firstOrCreate(
            ['email' => 'budi@uinssc.ac.id'],
            [
                'name' => 'Budi Pratama',
                'password' => Hash::make('password'),
            ]
        );
        $budi->syncRoles([$studentRole]);

        Profile::updateOrCreate(
            ['user_id' => $budi->id],
            [
                'nim' => '2026101002',
                'full_name' => 'Budi Pratama',
                'faculty' => 'Fakultas Syariah',
                'major' => 'Ilmu Falak',
                'phone' => '081298765432',
                'gender' => 'L',
                'birth_date' => '2006-08-20',
                'address' => 'Jl. Sudirman No. 88, Palembang',
            ]
        );

        HealthHistory::updateOrCreate(
            ['user_id' => $budi->id],
            [
                'blood_type' => 'O',
                'rhesus' => '+',
                'height_cm' => 174,
                'weight_kg' => 65,
                'allergies' => 'Tidak ada',
                'chronic_diseases' => 'Tidak ada',
                'current_medications' => 'Tidak ada',
                'past_surgeries' => 'Tidak ada',
                'is_smoker' => false,
                'vaccine_status' => 'Lengkap (Booster ke-1)',
                'emergency_contact_name' => 'Bambang Pratama (Ayah)',
                'emergency_contact_phone' => '081399887766',
            ]
        );

        $regBudi = Registration::updateOrCreate(
            ['user_id' => $budi->id],
            [
                'test_session_id' => $session1->id,
                'status' => 'cleared',
                'queue_code' => 'A-001',
            ]
        );

        Payment::updateOrCreate(
            ['registration_id' => $regBudi->id],
            [
                'amount' => 150000,
                'proof_file_path' => 'proofs/sample.jpg',
                'status' => 'verified',
                'bank_name' => 'Bank Syariah Indonesia (BSI)',
                'sender_name' => 'Budi Pratama',
                'verified_at' => Carbon::now()->subHours(5),
                'verified_by' => $staff->id,
            ]
        );

        // 7. Student 3: Siti (Completed with full pass & official certificate ready)
        $siti = User::firstOrCreate(
            ['email' => 'siti@uinssc.ac.id'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password'),
            ]
        );
        $siti->syncRoles([$studentRole]);

        Profile::updateOrCreate(
            ['user_id' => $siti->id],
            [
                'nim' => '2026101003',
                'full_name' => 'Siti Rahmawati',
                'faculty' => 'Fakultas Ekonomi Dan Bisnis Islam (FEBI)',
                'major' => 'Perbankan Syariah',
                'phone' => '081377889900',
                'gender' => 'P',
                'birth_date' => '2007-01-10',
                'address' => 'Komplek Griya Sejahtera Blok B-14, Palembang',
            ]
        );

        HealthHistory::updateOrCreate(
            ['user_id' => $siti->id],
            [
                'blood_type' => 'B',
                'rhesus' => '+',
                'height_cm' => 160,
                'weight_kg' => 52,
                'allergies' => 'Alergi Udang ringan',
                'chronic_diseases' => 'Tidak ada',
                'current_medications' => 'Tidak ada',
                'past_surgeries' => 'Apendiktomi (2022)',
                'is_smoker' => false,
                'vaccine_status' => 'Lengkap (Booster ke-2)',
                'emergency_contact_name' => 'Hj. Aminah (Ibu)',
                'emergency_contact_phone' => '081234009988',
            ]
        );

        $regSiti = Registration::updateOrCreate(
            ['user_id' => $siti->id],
            [
                'test_session_id' => $session1->id,
                'status' => 'completed',
                'queue_code' => 'A-002',
                'checked_in_at' => Carbon::now()->subHours(3),
                'completed_at' => Carbon::now()->subHours(1),
            ]
        );

        Payment::updateOrCreate(
            ['registration_id' => $regSiti->id],
            [
                'amount' => 150000,
                'proof_file_path' => 'proofs/sample.jpg',
                'status' => 'verified',
                'bank_name' => 'Bank Mandiri',
                'sender_name' => 'Siti Rahmawati',
                'verified_at' => Carbon::now()->subDays(1),
                'verified_by' => $staff->id,
            ]
        );

        StationResult::updateOrCreate(
            ['registration_id' => $regSiti->id, 'station_name' => 'urin'],
            [
                'status' => 'pass',
                'notes' => 'Reduksi (-), Albumin (-), Sedimen dalam batas normal. Hasil laboratorium steril.',
                'examiner_name' => 'dr. Hendra Sp.PK',
                'examined_at' => Carbon::now()->subHours(2),
            ]
        );

        StationResult::updateOrCreate(
            ['registration_id' => $regSiti->id, 'station_name' => 'napza'],
            [
                'status' => 'pass',
                'notes' => 'Skrining 6 Parameter NAPZA: AMP (-), MET (-), THC (-), MOP (-), COC (-), BZO (-). Negatif.',
                'examiner_name' => 'dr. Nurul Aini',
                'examined_at' => Carbon::now()->subHours(2)->addMinutes(20),
            ]
        );

        StationResult::updateOrCreate(
            ['registration_id' => $regSiti->id, 'station_name' => 'pemeriksaan'],
            [
                'status' => 'pass',
                'notes' => 'Tensi: 115/75 mmHg, Nadi: 78x/mnt. Visus mata ODS 6/6, Buta warna negatif. Fisik prima.',
                'examiner_name' => 'dr. Nurul Aini',
                'examined_at' => Carbon::now()->subHours(1)->addMinutes(15),
            ]
        );

        // 8. Student 4: Ahmad (Awaiting Verification - for staff verification practice)
        $ahmad = User::firstOrCreate(
            ['email' => 'ahmad@uinssc.ac.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
            ]
        );
        $ahmad->syncRoles([$studentRole]);

        Profile::updateOrCreate(
            ['user_id' => $ahmad->id],
            [
                'nim' => '2026101004',
                'full_name' => 'Ahmad Fauzi Rahman',
                'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'major' => 'Pendidikan Agama Islam',
                'phone' => '085266778899',
                'gender' => 'L',
                'birth_date' => '2006-11-25',
                'address' => 'Jl. Mayor Ruslan No. 101, Palembang',
            ]
        );

        HealthHistory::updateOrCreate(
            ['user_id' => $ahmad->id],
            [
                'blood_type' => 'AB',
                'rhesus' => '+',
                'height_cm' => 168,
                'weight_kg' => 60,
                'allergies' => 'Dingin/Debu',
                'chronic_diseases' => 'Tidak ada',
                'current_medications' => 'Antihistamin jika bersin',
                'past_surgeries' => 'Tidak ada',
                'is_smoker' => false,
                'vaccine_status' => 'Lengkap (Booster)',
                'emergency_contact_name' => 'Drs. H. M. Fauzi (Ayah)',
                'emergency_contact_phone' => '081277112233',
            ]
        );

        $regAhmad = Registration::updateOrCreate(
            ['user_id' => $ahmad->id],
            [
                'test_session_id' => $session2->id,
                'status' => 'awaiting_verification',
                'queue_code' => null,
            ]
        );

        Payment::updateOrCreate(
            ['registration_id' => $regAhmad->id],
            [
                'amount' => 150000,
                'proof_file_path' => 'proofs/sample.jpg',
                'status' => 'pending',
                'bank_name' => 'Bank Mandiri',
                'sender_name' => 'Ahmad Fauzi Rahman',
                'verified_at' => null,
                'verified_by' => null,
            ]
        );
    }
}
