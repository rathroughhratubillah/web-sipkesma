<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_login_using_campus_portal_credentials(): void
    {
        $response = $this->post('/login', [
            'email'    => '2281050030',
            'password' => '123',
        ]);

        $response->assertRedirect('/beranda');

        // Pastikan user tersimpan di DB
        $this->assertDatabaseHas('users', [
            'name'  => 'LANGIT KALA FAJAR',
            'email' => 'kalafajar@gmail.com',
        ]);

        // Pastikan profil otomatis terisi dari API
        $this->assertDatabaseHas('profiles', [
            'nim'       => '2281050030',
            'full_name' => 'LANGIT KALA FAJAR',
            'major'     => 'Tadris Matematika',
        ]);

        $user = User::where('email', 'kalafajar@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('student'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_using_campus_portal_credentials(): void
    {
        $response = $this->post('/login', [
            'email'    => '198501012010121001',
            'password' => '123',
        ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('users', [
            'email' => 'admin.it@uinssc.ac.id',
        ]);

        $admin = User::where('email', 'admin.it@uinssc.ac.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertAuthenticatedAs($admin);
    }
}

