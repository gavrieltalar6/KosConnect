<?php

namespace Database\Seeders;

use App\Enums\TenantStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tenants')->insert([
            [
                'user_id' => 4, // Budi Santoso
                'room_id' => 1, // K-01
                'full_name' => 'Budi Santoso',
                'phone' => '081234567890',
                'origin_address' => 'Jakarta Selatan',
                'guardian_phone' => '089876543210',
                'telegram_chat_id' => 123456789,
                'telegram_link_token' => 'abc123xyz',
                'check_in_date' => '2024-01-15',
                'due_day' => 15,
                'status' => TenantStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5, // Siti Rahayu
                'room_id' => 2, // K-02
                'full_name' => 'Siti Rahayu',
                'phone' => '081345678901',
                'origin_address' => 'Bandung',
                'guardian_phone' => '089987654321',
                'telegram_chat_id' => 987654321,
                'telegram_link_token' => 'def456uvw',
                'check_in_date' => '2024-02-10',
                'due_day' => 10,
                'status' => TenantStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6, // Joko Prakoso
                'room_id' => 3, // K-03
                'full_name' => 'Joko Prakoso',
                'phone' => '081567890123',
                'origin_address' => 'Surabaya',
                'guardian_phone' => '089765432109',
                'telegram_chat_id' => 567890123,
                'telegram_link_token' => 'ghi789rst',
                'check_in_date' => '2024-03-01',
                'due_day' => 5,
                'status' => TenantStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 7, // Lestari Wijaya
                'room_id' => 4, // K-04
                'full_name' => 'Lestari Wijaya',
                'phone' => '081456789012',
                'origin_address' => 'Malang',
                'guardian_phone' => '089654321098',
                'telegram_chat_id' => 345678901,
                'telegram_link_token' => 'jkl098mno',
                'check_in_date' => '2024-04-20',
                'due_day' => 25,
                'status' => TenantStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8, // Andi Saputra
                'room_id' => 5, // K-05
                'full_name' => 'Andi Saputra',
                'phone' => '081678901234',
                'origin_address' => 'Bandung',
                'guardian_phone' => '089567890123',
                'telegram_chat_id' => 234567890,
                'telegram_link_token' => 'pqr345tuv',
                'check_in_date' => '2024-05-01',
                'due_day' => 1,
                'status' => TenantStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}