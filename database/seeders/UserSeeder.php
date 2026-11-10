<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Pemilik Kos',
                'email' => 'pemilik@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PEMILIK->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pengurus Satu',
                'email' => 'pengurus1@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGURUS->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pengurus Dua',
                'email' => 'pengurus2@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGURUS->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tenants users
            [
                'name' => 'Budi Santoso',
                'email' => 'penghuni1@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGGUNI->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'penghuni2@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGGUNI->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Joko Prakoso',
                'email' => 'penghuni3@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGGUNI->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lestari Wijaya',
                'email' => 'penghuni4@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGGUNI->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Andi Saputra',
                'email' => 'penghuni5@kosconnect.test',
                'password' => Hash::make('password'),
                'role' => Role::PENGGUNI->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}