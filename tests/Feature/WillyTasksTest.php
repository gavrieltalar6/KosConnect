<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\RoomStatus;
use App\Enums\TenantStatus;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WillyTasksTest extends TestCase
{
    use RefreshDatabase;

    private function createPemilik(): User
    {
        return User::create([
            'name' => 'Pemilik Test',
            'email' => 'pemilik_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => Role::PEMILIK,
            'is_active' => true,
        ]);
    }

    private function createPengurus(): User
    {
        return User::create([
            'name' => 'Pengurus Test',
            'email' => 'pengurus_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => Role::PENGURUS,
            'is_active' => true,
        ]);
    }

    private function createPenghuni(Room $room = null): array
    {
        $user = User::create([
            'name' => 'Penghuni Test',
            'email' => 'penghuni_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => Role::PENGGUNI,
            'is_active' => true,
        ]);

        $tenant = Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room?->id,
            'full_name' => 'Penghuni Test',
            'phone' => '08123456789',
            'origin_address' => 'Jl. Test No. 123',
            'guardian_phone' => '08987654321',
            'check_in_date' => now()->toDateString(),
            'due_day' => 15,
            'status' => TenantStatus::ACTIVE,
        ]);

        return [$user, $tenant];
    }

    // ==========================================
    // TUGAS 1: Pembuatan Akun Penghuni
    // ==========================================

    public function test_pemilik_can_access_tenant_creation_page(): void
    {
        $pemilik = $this->createPemilik();

        $response = $this->actingAs($pemilik)->get(route('pemilik.tenants.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Penghuni Baru');
    }

    public function test_pengurus_can_access_tenant_creation_page(): void
    {
        $pengurus = $this->createPengurus();

        $response = $this->actingAs($pengurus)->get(route('pemilik.tenants.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Penghuni Baru');
    }

    public function test_pengurus_can_create_tenant_account_and_user(): void
    {
        $pengurus = $this->createPengurus();
        $room = Room::create([
            'room_number' => 'A101',
            'floor' => 1,
            'monthly_price' => 1500000,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $tenantData = [
            'full_name' => 'Anak Kos Baru',
            'email' => 'anakkosbaru@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '081122334455',
            'origin_address' => 'Surabaya',
            'guardian_phone' => '089988776655',
            'room_id' => $room->id,
            'check_in_date' => now()->toDateString(),
            'due_day' => 10,
        ];

        $response = $this->actingAs($pengurus)->post(route('pemilik.tenants.store'), $tenantData);

        $response->assertRedirect(route('pemilik.tenants.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Anak Kos Baru',
            'email' => 'anakkosbaru@test.com',
            'role' => Role::PENGGUNI->value,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('tenants', [
            'full_name' => 'Anak Kos Baru',
            'phone' => '081122334455',
            'room_id' => $room->id,
        ]);
    }

    public function test_tenant_creation_fails_when_password_confirmation_does_not_match(): void
    {
        $pengurus = $this->createPengurus();

        $tenantData = [
            'full_name' => 'Penghuni Mismatch',
            'email' => 'mismatch@test.com',
            'password' => 'passwordA',
            'password_confirmation' => 'passwordB',
            'phone' => '081122334455',
            'origin_address' => 'Bandung',
            'guardian_phone' => '089988776655',
            'check_in_date' => now()->toDateString(),
            'due_day' => 10,
        ];

        $response = $this->actingAs($pengurus)->post(route('pemilik.tenants.store'), $tenantData);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@test.com']);
    }

    // ==========================================
    // TUGAS 2: Halaman Bantuan Reset Password (Input 2x)
    // ==========================================

    public function test_pemilik_and_pengurus_can_access_password_resets_index(): void
    {
        $pemilik = $this->createPemilik();
        $pengurus = $this->createPengurus();
        [$penghuniUser, $tenant] = $this->createPenghuni();

        // Pemilik akses
        $responsePemilik = $this->actingAs($pemilik)->get(route('password_resets.index'));
        $responsePemilik->assertStatus(200);
        $responsePemilik->assertSee('Bantuan Reset Password');
        $responsePemilik->assertSee($pengurus->name);
        $responsePemilik->assertSee($penghuniUser->name);

        // Pengurus akses
        $responsePengurus = $this->actingAs($pengurus)->get(route('password_resets.index'));
        $responsePengurus->assertStatus(200);
        $responsePengurus->assertSee('Bantuan Reset Password');
        $responsePengurus->assertSee($penghuniUser->name);

        // Test filter role penghuni
        $responseFilterPenghuni = $this->actingAs($pengurus)->get(route('password_resets.index', ['role' => 'penghuni']));
        $responseFilterPenghuni->assertStatus(200);
        $this->assertTrue($responseFilterPenghuni->viewData('users')->contains($penghuniUser));
        $this->assertFalse($responseFilterPenghuni->viewData('users')->contains($pengurus));

        // Test filter role pengurus
        $responseFilterPengurus = $this->actingAs($pengurus)->get(route('password_resets.index', ['role' => 'pengurus']));
        $responseFilterPengurus->assertStatus(200);
        $this->assertTrue($responseFilterPengurus->viewData('users')->contains($pengurus));
        $this->assertFalse($responseFilterPengurus->viewData('users')->contains($penghuniUser));
    }

    public function test_pengurus_can_view_edit_password_page_with_two_inputs(): void
    {
        $pengurus = $this->createPengurus();
        [$penghuniUser, $tenant] = $this->createPenghuni();

        $response = $this->actingAs($pengurus)->get(route('password_resets.edit', $penghuniUser));
        $response->assertStatus(200);
        $response->assertSee('Password Baru');
        $response->assertSee('Ulangi Password Baru (Konfirmasi)');
        $response->assertSee('2 kali');
    }

    public function test_password_reset_fails_if_password_confirmation_does_not_match(): void
    {
        $pengurus = $this->createPengurus();
        [$penghuniUser, $tenant] = $this->createPenghuni();

        $oldPasswordHash = $penghuniUser->password;

        $response = $this->actingAs($pengurus)->put(route('password_resets.update', $penghuniUser), [
            'password' => 'newPassword123',
            'password_confirmation' => 'differentPassword',
        ]);

        $response->assertSessionHasErrors('password');
        $penghuniUser->refresh();
        $this->assertEquals($oldPasswordHash, $penghuniUser->password);
    }

    public function test_password_reset_succeeds_when_passwords_match_twice(): void
    {
        $pengurus = $this->createPengurus();
        [$penghuniUser, $tenant] = $this->createPenghuni();

        $response = $this->actingAs($pengurus)->put(route('password_resets.update', $penghuniUser), [
            'password' => 'freshPass2026',
            'password_confirmation' => 'freshPass2026',
        ]);

        $response->assertRedirect(route('password_resets.index'));
        $response->assertSessionHas('success');

        $penghuniUser->refresh();
        $this->assertTrue(Hash::check('freshPass2026', $penghuniUser->password));

        // Verifikasi pengguna dapat login dengan password baru
        $loginResponse = $this->post('/login', [
            'email' => $penghuniUser->email,
            'password' => 'freshPass2026',
        ]);
        $loginResponse->assertRedirect('/penghuni/dashboard');
    }

    public function test_pemilik_can_reset_pengurus_password_who_forgot_it(): void
    {
        $pemilik = $this->createPemilik();
        $pengurus = $this->createPengurus();

        $response = $this->actingAs($pemilik)->put(route('password_resets.update', $pengurus), [
            'password' => 'pengurusBaru123',
            'password_confirmation' => 'pengurusBaru123',
        ]);

        $response->assertRedirect(route('password_resets.index'));
        $pengurus->refresh();
        $this->assertTrue(Hash::check('pengurusBaru123', $pengurus->password));

        // Verifikasi pengurus dapat login dengan password baru
        $loginResponse = $this->post('/login', [
            'email' => $pengurus->email,
            'password' => 'pengurusBaru123',
        ]);
        $loginResponse->assertRedirect('/pengurus/dashboard');
    }

    public function test_cannot_reset_pemilik_password_via_reset_form(): void
    {
        $pengurus = $this->createPengurus();
        $pemilik = $this->createPemilik();

        $response = $this->actingAs($pengurus)->get(route('password_resets.edit', $pemilik));
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($pengurus)->put(route('password_resets.update', $pemilik), [
            'password' => 'hackedPassword',
            'password_confirmation' => 'hackedPassword',
        ]);
        $updateResponse->assertStatus(403);
    }
}
