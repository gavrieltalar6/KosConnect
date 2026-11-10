<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/pemilik/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_penghuni_cannot_access_pemilik_dashboard(): void
    {
        $penghuni = User::create([
            'name' => 'Penghuni Test',
            'email' => 'penghuni@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PENGGUNI->value,
            'is_active' => true,
        ]);

        $response = $this->actingAs($penghuni)->get('/pemilik/dashboard');
        $response->assertStatus(403);
    }

    public function test_pemilik_can_access_pemilik_dashboard(): void
    {
        $pemilik = User::create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => true,
        ]);

        $response = $this->actingAs($pemilik)->get('/pemilik/dashboard');
        $response->assertStatus(200);
    }
}
