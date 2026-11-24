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
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Migrasi jika ada role staff lama
        $oldStaffRole = Role::where('name', 'staff')->first();
        if ($oldStaffRole) {
            User::role('staff')->get()->each(function ($u) use ($adminRole) {
                $u->syncRoles([$adminRole]);
            });
            $oldStaffRole->delete();
        }

        // 2. Admin Utama PPKMB
        $admin = User::firstOrCreate(
            ['email' => 'admin@uinssc.ac.id'],
            [
                'name' => 'Administrator PPKMB',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 3. Medis / Dokter Penanggung Jawab (dr. Nurul Aini, Sp.PK)
        $staff = User::firstOrCreate(
            ['email' => 'petugas@uinssc.ac.id'],
            [
                'name' => 'dr. Nurul Aini, Sp.PK',
                'password' => Hash::make('password'),
            ]
        );
        $staff->syncRoles([$adminRole]);

        // 4. Test Sessions
        $session1 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Pagi Gelombang 1 (08:00 - 10:00)'],
            [
                'session_date' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'quota' => 100,
                'is_active' => true,
            ]
        );

        $session2 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Siang Gelombang 1 (10:30 - 12:30)'],
            [
                'session_date' => Carbon::today()->addDays(1)->format('Y-m-d'),
                'start_time' => '10:30:00',
                'end_time' => '12:30:00',
                'quota' => 100,
                'is_active' => true,
            ]
        );

        $session3 = TestSession::firstOrCreate(
            ['session_name' => 'Sesi Pagi Gelombang 2 (08:00 - 10:00)'],
            [
                'session_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'quota' => 120,
                'is_active' => true,
            ]
        );

        // 5. Data Dummy 10 Mahasiswa Lengkap
        $studentsData = [
            // 1. Ratu: Awaiting Payment (siap tes fitur pembayaran SeaBank & DANA)
            [
                'name' => 'Ratu',
                'email' => 'ratu@uinssc.ac.id',
                'nim' => '2026101001',
                'full_name' => 'Ratu Ayu Maharani',
                'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'major' => 'Informatika',
                'phone' => '081234567890',
                'gender' => 'P',
                'birth_date' => '2007-04-15',
                'address' => 'Jl. Merdeka No. 12, Kota Palembang',
                'health' => [
                    'blood_type' => 'O', 'rhesus' => '+', 'height_cm' => 162, 'weight_kg' => 50,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-1)',
                    'emergency_contact_name' => 'Drs. H. Maharani (Ayah)', 'emergency_contact_phone' => '081299887711',
                ],
                'session' => $session1,
                'status' => 'awaiting_payment',
                'queue_code' => null,
                'payment' => null,
                'stations' => null,
            ],

            // 2. Budi: Cleared (Sudah bayar via SeaBank, tiket A-001 terbit)
            [
                'name' => 'Budi Pratama',
                'email' => 'budi@uinssc.ac.id',
                'nim' => '2026101002',
                'full_name' => 'Budi Pratama',
                'faculty' => 'Fakultas Syariah',
                'major' => 'Ilmu Falak',
                'phone' => '081298765432',
                'gender' => 'L',
                'birth_date' => '2006-08-20',
                'address' => 'Jl. Sudirman No. 88, Palembang',
                'health' => [
                    'blood_type' => 'O', 'rhesus' => '+', 'height_cm' => 174, 'weight_kg' => 65,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-1)',
                    'emergency_contact_name' => 'Bambang Pratama (Ayah)', 'emergency_contact_phone' => '081399887766',
                ],
                'session' => $session1,
                'status' => 'cleared',
                'queue_code' => 'A-001',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'SeaBank',
                    'sender_name' => 'Budi Pratama',
                    'verified_at' => Carbon::now()->subHours(6),
                    'verified_by' => $staff->id,
                ],
                'stations' => null,
            ],

            // 3. Siti: Completed (Lulus 3 Stasiun Medis, Sertifikat Telah Terbit!)
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti@uinssc.ac.id',
                'nim' => '2026101003',
                'full_name' => 'Siti Rahmawati',
                'faculty' => 'Fakultas Ekonomi Dan Bisnis Islam (FEBI)',
                'major' => 'Perbankan Syariah',
                'phone' => '081377889900',
                'gender' => 'P',
                'birth_date' => '2007-01-10',
                'address' => 'Komplek Griya Sejahtera Blok B-14, Palembang',
                'health' => [
                    'blood_type' => 'B', 'rhesus' => '+', 'height_cm' => 160, 'weight_kg' => 52,
                    'allergies' => 'Alergi Udang ringan', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Apendiktomi (2022)', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-2)',
                    'emergency_contact_name' => 'Hj. Aminah (Ibu)', 'emergency_contact_phone' => '081234009988',
                ],
                'session' => $session1,
                'status' => 'completed',
                'queue_code' => 'A-002',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'DANA',
                    'sender_name' => 'Siti Rahmawati',
                    'verified_at' => Carbon::now()->subDays(1),
                    'verified_by' => $staff->id,
                ],
                'stations' => [
                    ['station_name' => 'urin', 'status' => 'pass', 'notes' => 'Uji Buku Ishihara 38 plates: 38/38 terbaca benar. Persepsi warna normal (bebas buta warna).', 'examiner_name' => 'dr. Hendra Sp.PK'],
                    ['station_name' => 'napza', 'status' => 'pass', 'notes' => 'Skrining 6 Parameter NAPZA: AMP (-), MET (-), THC (-), MOP (-), COC (-), BZO (-). Non-Reaktif.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                    ['station_name' => 'pemeriksaan', 'status' => 'pass', 'notes' => 'Tensi: 115/75 mmHg, Nadi: 78x/mnt. Visus mata ODS 6/6, Buta warna negatif. Fisik prima.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                ],
            ],

            // 4. Ahmad: Awaiting Verification (Bayar via SeaBank, menunggu persetujuan admin)
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@uinssc.ac.id',
                'nim' => '2026101004',
                'full_name' => 'Ahmad Fauzi Rahman',
                'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'major' => 'Pendidikan Agama Islam',
                'phone' => '085266778899',
                'gender' => 'L',
                'birth_date' => '2006-11-25',
                'address' => 'Jl. Mayor Ruslan No. 101, Palembang',
                'health' => [
                    'blood_type' => 'AB', 'rhesus' => '+', 'height_cm' => 168, 'weight_kg' => 60,
                    'allergies' => 'Dingin/Debu ringan', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster)',
                    'emergency_contact_name' => 'Drs. H. M. Fauzi (Ayah)', 'emergency_contact_phone' => '081277112233',
                ],
                'session' => $session2,
                'status' => 'awaiting_verification',
                'queue_code' => null,
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'pending',
                    'bank_name' => 'SeaBank',
                    'sender_name' => 'Ahmad Fauzi Rahman',
                    'verified_at' => null,
                    'verified_by' => null,
                ],
                'stations' => null,
            ],

            // 5. Nadia: Completed (Lulus 3 Stasiun Medis, Sertifikat Telah Terbit!)
            [
                'name' => 'Nadia Putri',
                'email' => 'nadia@uinssc.ac.id',
                'nim' => '2026101005',
                'full_name' => 'Nadia Putri Ananda',
                'faculty' => 'Fakultas Ushuluddin Dan Arab',
                'major' => "Ilmu Al-Qur'an dan Tafsir",
                'phone' => '087811223344',
                'gender' => 'P',
                'birth_date' => '2007-03-18',
                'address' => 'Jl. Veteran No. 45, Palembang',
                'health' => [
                    'blood_type' => 'A', 'rhesus' => '+', 'height_cm' => 158, 'weight_kg' => 48,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-2)',
                    'emergency_contact_name' => 'Ananda Saputra (Kakak)', 'emergency_contact_phone' => '087899001122',
                ],
                'session' => $session1,
                'status' => 'completed',
                'queue_code' => 'A-003',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'SeaBank',
                    'sender_name' => 'Nadia Putri Ananda',
                    'verified_at' => Carbon::now()->subDays(2),
                    'verified_by' => $staff->id,
                ],
                'stations' => [
                    ['station_name' => 'urin', 'status' => 'pass', 'notes' => 'Uji Buku Ishihara: persepsi warna normal, bebas buta warna total maupun parsial.', 'examiner_name' => 'dr. Hendra Sp.PK'],
                    ['station_name' => 'napza', 'status' => 'pass', 'notes' => '6 Parameter NAPZA negatif lengkap. Bersih zat adiktif.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                    ['station_name' => 'pemeriksaan', 'status' => 'pass', 'notes' => 'Tensi 110/70 mmHg, penglihatan baik, fisik prima & sehat.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                ],
            ],

            // 6. Rizky: In Progress (Sedang jalani pemeriksaan stasiun medis)
            [
                'name' => 'Muhammad Rizky',
                'email' => 'rizky@uinssc.ac.id',
                'nim' => '2026101006',
                'full_name' => 'Muhammad Rizky Ramadhan',
                'faculty' => 'Fakultas Dakwah Dan Komunikasi Islam (FDKI)',
                'major' => 'Komunikasi dan Penyiaran Islam',
                'phone' => '082155667788',
                'gender' => 'L',
                'birth_date' => '2006-10-05',
                'address' => 'Jl. Demang Lebar Daun No. 20, Palembang',
                'health' => [
                    'blood_type' => 'O', 'rhesus' => '+', 'height_cm' => 170, 'weight_kg' => 63,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster)',
                    'emergency_contact_name' => 'Ramadhan Syah (Ayah)', 'emergency_contact_phone' => '082199887766',
                ],
                'session' => $session2,
                'status' => 'in_progress',
                'queue_code' => 'A-004',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'DANA',
                    'sender_name' => 'Muhammad Rizky',
                    'verified_at' => Carbon::now()->subHours(4),
                    'verified_by' => $staff->id,
                ],
                'stations' => [
                    ['station_name' => 'urin', 'status' => 'pass', 'notes' => 'Uji Buku Ishihara 38 plates terbaca akurat. Tidak ada buta warna.', 'examiner_name' => 'dr. Hendra Sp.PK'],
                    ['station_name' => 'napza', 'status' => 'pass', 'notes' => 'Screening test 6 panel NAPZA negatif.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                ],
            ],

            // 7. Zahra: Awaiting Verification (Bayar via DANA, menunggu admin)
            [
                'name' => 'Zahra Aulia',
                'email' => 'zahra@uinssc.ac.id',
                'nim' => '2026101007',
                'full_name' => 'Zahra Aulia Maharani',
                'faculty' => 'Fakultas Ekonomi Dan Bisnis Islam (FEBI)',
                'major' => 'Akuntansi Syariah',
                'phone' => '089633445566',
                'gender' => 'P',
                'birth_date' => '2007-06-22',
                'address' => 'Jl. R. Sukamto No. 77, Palembang',
                'health' => [
                    'blood_type' => 'B', 'rhesus' => '+', 'height_cm' => 159, 'weight_kg' => 49,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster)',
                    'emergency_contact_name' => 'Dra. Nurhayati (Ibu)', 'emergency_contact_phone' => '089611223344',
                ],
                'session' => $session3,
                'status' => 'awaiting_verification',
                'queue_code' => null,
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'pending',
                    'bank_name' => 'DANA',
                    'sender_name' => 'Zahra Aulia M',
                    'verified_at' => null,
                    'verified_by' => null,
                ],
                'stations' => null,
            ],

            // 8. Dimas: Completed (Lulus 3 Stasiun Medis, Sertifikat Telah Terbit!)
            [
                'name' => 'Dimas Arya',
                'email' => 'dimas@uinssc.ac.id',
                'nim' => '2026101008',
                'full_name' => 'Dimas Arya Pratama',
                'faculty' => 'Fakultas Syariah',
                'major' => "Hukum Ekonomi Syari'ah (Muamalah)",
                'phone' => '085788991122',
                'gender' => 'L',
                'birth_date' => '2006-09-14',
                'address' => 'Jl. Kolonel Atmo No. 33, Palembang',
                'health' => [
                    'blood_type' => 'A', 'rhesus' => '+', 'height_cm' => 172, 'weight_kg' => 66,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-2)',
                    'emergency_contact_name' => 'H. Pratama (Ayah)', 'emergency_contact_phone' => '085711223399',
                ],
                'session' => $session1,
                'status' => 'completed',
                'queue_code' => 'A-005',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'SeaBank',
                    'sender_name' => 'Dimas Arya Pratama',
                    'verified_at' => Carbon::now()->subDays(1),
                    'verified_by' => $staff->id,
                ],
                'stations' => [
                    ['station_name' => 'urin', 'status' => 'pass', 'notes' => 'Uji Buku Ishihara: persepsi warna normal tanpa kendala.', 'examiner_name' => 'dr. Hendra Sp.PK'],
                    ['station_name' => 'napza', 'status' => 'pass', 'notes' => 'Panel narkoba 6 zat adiktif negatif penuh.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                    ['station_name' => 'pemeriksaan', 'status' => 'pass', 'notes' => 'Tensi 120/80 mmHg, buta warna negatif, tubuh atletis & bugar.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                ],
            ],

            // 9. Annisa: Cleared (Bayar via DANA verified, tiket A-006)
            [
                'name' => 'Annisa Fatimah',
                'email' => 'annisa@uinssc.ac.id',
                'nim' => '2026101009',
                'full_name' => 'Annisa Fatimah Azzahra',
                'faculty' => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'major' => 'Tadris Matematika',
                'phone' => '081922334455',
                'gender' => 'P',
                'birth_date' => '2007-02-28',
                'address' => 'Jl. Basuki Rahmat No. 19, Palembang',
                'health' => [
                    'blood_type' => 'AB', 'rhesus' => '+', 'height_cm' => 163, 'weight_kg' => 53,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster)',
                    'emergency_contact_name' => 'Fatimah Dewi (Ibu)', 'emergency_contact_phone' => '081900112233',
                ],
                'session' => $session2,
                'status' => 'cleared',
                'queue_code' => 'A-006',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'DANA',
                    'sender_name' => 'Annisa Fatimah',
                    'verified_at' => Carbon::now()->subHours(8),
                    'verified_by' => $staff->id,
                ],
                'stations' => null,
            ],

            // 10. Faris: Completed (Lulus 3 Stasiun Medis, Sertifikat Telah Terbit!)
            [
                'name' => 'Faris Al-Fatih',
                'email' => 'faris@uinssc.ac.id',
                'nim' => '2026101010',
                'full_name' => 'Faris Al-Fatih',
                'faculty' => 'Fakultas Ushuluddin Dan Arab',
                'major' => 'Aqidah dan Filsafat Islam',
                'phone' => '085377889911',
                'gender' => 'L',
                'birth_date' => '2006-12-11',
                'address' => 'Jl. Jenderal Sudirman KM 3.5, Palembang',
                'health' => [
                    'blood_type' => 'O', 'rhesus' => '+', 'height_cm' => 176, 'weight_kg' => 68,
                    'allergies' => 'Tidak ada', 'chronic_diseases' => 'Tidak ada', 'current_medications' => 'Tidak ada',
                    'past_surgeries' => 'Tidak ada', 'is_smoker' => false, 'vaccine_status' => 'Lengkap (Booster ke-2)',
                    'emergency_contact_name' => 'Ust. Mansyur (Ayah)', 'emergency_contact_phone' => '085399887722',
                ],
                'session' => $session1,
                'status' => 'completed',
                'queue_code' => 'A-007',
                'payment' => [
                    'amount' => 150000,
                    'proof_file_path' => 'proofs/sample.jpg',
                    'status' => 'verified',
                    'bank_name' => 'SeaBank',
                    'sender_name' => 'Faris Al-Fatih',
                    'verified_at' => Carbon::now()->subDays(1),
                    'verified_by' => $staff->id,
                ],
                'stations' => [
                    ['station_name' => 'urin', 'status' => 'pass', 'notes' => 'Uji Ishihara normal, seluruh pelat warna teridentifikasi tepat.', 'examiner_name' => 'dr. Hendra Sp.PK'],
                    ['station_name' => 'napza', 'status' => 'pass', 'notes' => 'Tes bebas narkoba 6 parameter negatif sempurna.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                    ['station_name' => 'pemeriksaan', 'status' => 'pass', 'notes' => 'Kondisi fisik prima, tensi 118/76 mmHg, tidak buta warna.', 'examiner_name' => 'dr. Nurul Aini, Sp.PK'],
                ],
            ],
        ];

        foreach ($studentsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $user->syncRoles([$studentRole]);

            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $data['nim'],
                    'full_name' => $data['full_name'],
                    'faculty' => $data['faculty'],
                    'major' => $data['major'],
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'birth_date' => $data['birth_date'],
                    'address' => $data['address'],
                ]
            );

            if (!empty($data['health'])) {
                HealthHistory::updateOrCreate(
                    ['user_id' => $user->id],
                    $data['health']
                );
            }

            $regData = [
                'test_session_id' => $data['session']->id,
                'status' => $data['status'],
                'queue_code' => $data['queue_code'],
            ];

            if ($data['status'] === 'completed') {
                $regData['checked_in_at'] = Carbon::now()->subHours(4);
                $regData['completed_at'] = Carbon::now()->subHours(1);
            } elseif ($data['status'] === 'in_progress') {
                $regData['checked_in_at'] = Carbon::now()->subHours(2);
            }

            $registration = Registration::updateOrCreate(
                ['user_id' => $user->id],
                $regData
            );

            if (!empty($data['payment'])) {
                Payment::updateOrCreate(
                    ['registration_id' => $registration->id],
                    $data['payment']
                );
            }

            if (!empty($data['stations'])) {
                foreach ($data['stations'] as $st) {
                    StationResult::updateOrCreate(
                        ['registration_id' => $registration->id, 'station_name' => $st['station_name']],
                        [
                            'status' => $st['status'],
                            'notes' => $st['notes'],
                            'examiner_name' => $st['examiner_name'],
                            'examined_at' => Carbon::now()->subHours(2),
                        ]
                    );
                }
            }
        }
    }
}
