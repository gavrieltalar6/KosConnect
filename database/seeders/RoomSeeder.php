<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rooms')->insert([
            [
                'room_number' => 'K-01',
                'floor' => 1,
                'monthly_price' => 500000.00,
                'status' => RoomStatus::AVAILABLE->value,
                'description' => 'Kamar tidur nyaman dengan AC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_number' => 'K-02',
                'floor' => 1,
                'monthly_price' => 550000.00,
                'status' => RoomStatus::AVAILABLE->value,
                'description' => 'Kamar dengan balkon',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_number' => 'K-03',
                'floor' => 2,
                'monthly_price' => 600000.00,
                'status' => RoomStatus::OCCUPIED->value,
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_number' => 'K-04',
                'floor' => 2,
                'monthly_price' => 600000.00,
                'status' => RoomStatus::MAINTENANCE->value,
                'description' => 'Sedang renovasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_number' => 'K-05',
                'floor' => 2,
                'monthly_price' => 550000.00,
                'status' => RoomStatus::AVAILABLE->value,
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}