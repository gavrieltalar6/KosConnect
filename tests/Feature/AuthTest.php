<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'pemilik@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/pemilik/dashboard');
    }

    public function test_inactive_users_cannot_login(): void
    {
        User::create([
            'name' => 'Inactive Test',
            'email' => 'inactive@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout(): void
    {
        $user = User::create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
