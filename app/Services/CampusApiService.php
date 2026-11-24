<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampusApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    
    {
        $this->baseUrl = rtrim(config('services.campus.base_url', 'https://bridge.uinssc.ac.id'), '/');
        $this->timeout = (int) config('services.campus.timeout', 8);
    }

    /**
     * Login ke API Portal Kampus v2
     * Endpoint: POST /api/v2/portal/login
     */
    public function login(string $username, string $password): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v2/portal/login", [
                    'username' => trim($username),
                    'password' => $password,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json['status']) && !empty($json['data'])) {
                    return $json['data'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Koneksi API Kampus Login gagal: " . $e->getMessage());
        }

        // Fallback Simulasi Pengujian Mandiri Sesuai Dokumentasi Resmi API v2
        // 1. Akun Admin / Pegawai Kampus (NIP / Username Pegawai)
        if ((trim($username) === '198501012010121001' || strtolower(trim($username)) === 'admin_kampus' || strtolower(trim($username)) === 'pegawai') && $password === '123') {
            return [
                'username' => '198501012010121001',
                'nama'     => 'Dr. H. Ahmad Fauzi, M.Kom (Admin IT Kampus)',
                'role'     => 'admin',
                'nip'      => '198501012010121001',
                'email'    => 'admin.it@uinssc.ac.id',
                'token'    => 'sample-sanctum-token-admin-kampus',
            ];
        }

        // 2. Akun Petugas Medis / Staf Klinik Kampus
        if ((trim($username) === '199002152015042002' || strtolower(trim($username)) === 'petugas_medis' || strtolower(trim($username)) === 'staf_klinik') && $password === '123') {
            return [
                'username' => '199002152015042002',
                'nama'     => 'dr. Nurul Aini (Dokter Klinik Kampus)',
                'role'     => 'admin',
                'nip'      => '199002152015042002',
                'email'    => 'dokter.klinik@uinssc.ac.id',
                'token'    => 'sample-sanctum-token-petugas-kampus',
            ];
        }

        // 3. Fallback Akun Mahasiswa
        if (trim($username) === '2281050030' && $password === '123') {
            return [
                'username' => '2281050030',
                'nama'     => 'LANGIT KALA FAJAR',
                'role'     => 'mahasiswa',
                'token'    => 'sample-sanctum-token-langit-kala-fajar',
            ];
        }

        return null;
    }

    /**
     * Ambil Profil Lengkap Pengguna dari API Portal Kampus v2
     * Endpoint: GET /api/v2/profil
     */
    public function getProfile(string $token): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v2/profil");

            if ($response->successful()) {
                $json = $response->json();
                if (!empty($json['status']) && !empty($json['data'])) {
                    return $json['data'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Koneksi API Kampus Profil gagal: " . $e->getMessage());
        }

        // Fallback data simulasi profil mahasiswa sesuai dokumentasi API v2
        if ($token === 'sample-sanctum-token-langit-kala-fajar') {
            return [
                'nim'                => '2281050030',
                'nama'               => 'LANGIT KALA FAJAR',
                'no_hp'              => '08123456789',
                'email'              => 'kalafajar@gmail.com',
                'tempat_lahir'       => 'Cirebon',
                'tanggal_lahir'      => '2005-02-25',
                'jenis_kelamin'      => 'L',
                'fakultas_nama'      => 'Fakultas Ilmu Tarbiyah Dan Keguruan (FITK)',
                'program_studi_nama' => 'Tadris Matematika',
                'alamat'             => 'Jl. Perjuangan By Pass Cirebon',
                'status'             => 'AKTIF',
            ];
        }

        return null;
    }
}

