<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_data_privacy_policy(): void
    {
        $room = Room::create([
            'room_number' => 'A1',
            'monthly_price' => 500000,
        ]);

        $userA = User::create([
            'name' => 'Penghuni A',
            'email' => 'a@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PENGGUNI->value,
            'is_active' => true,
        ]);

        $tenantA = Tenant::create([
            'user_id' => $userA->id,
            'room_id' => $room->id,
            'full_name' => 'Penghuni A',
            'phone' => '0811111111',
            'origin_address' => 'Jakarta',
            'guardian_phone' => '0822222222',
            'check_in_date' => now(),
            'due_day' => 10,
        ]);

        $userB = User::create([
            'name' => 'Penghuni B',
            'email' => 'b@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PENGGUNI->value,
            'is_active' => true,
        ]);

        $tenantB = Tenant::create([
            'user_id' => $userB->id,
            'room_id' => $room->id,
            'full_name' => 'Penghuni B',
            'phone' => '0833333333',
            'origin_address' => 'Bandung',
            'guardian_phone' => '0844444444',
            'check_in_date' => now(),
            'due_day' => 10,
        ]);

        $pemilik = User::create([
            'name' => 'Pemilik',
            'email' => 'pemilik@test.com',
            'password' => bcrypt('password'),
            'role' => Role::PEMILIK->value,
            'is_active' => true,
        ]);

        // Pemilik can view tenant A
        $this->assertTrue($pemilik->can('view', $tenantA));

        // Penghuni A can view tenant A
        $this->assertTrue($userA->can('view', $tenantA));

        // Penghuni A CANNOT view tenant B
        $this->assertFalse($userA->can('view', $tenantB));
    }
}
