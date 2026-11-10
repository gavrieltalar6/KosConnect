<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('invoices')->insert([
            [
                'tenant_id' => 1,
                'period' => '2024-01',
                'amount' => 500000.00,
                'due_date' => '2024-01-15',
                'status' => InvoiceStatus::UNPAID->value,
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 1,
                'period' => '2024-02',
                'amount' => 500000.00,
                'due_date' => '2024-02-15',
                'status' => InvoiceStatus::PAID->value,
                'paid_at' => '2024-02-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 2,
                'period' => '2024-01',
                'amount' => 550000.00,
                'due_date' => '2024-01-15',
                'status' => InvoiceStatus::UNPAID->value,
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 3,
                'period' => '2024-01',
                'amount' => 600000.00,
                'due_date' => '2024-01-15',
                'status' => InvoiceStatus::UNPAID->value,
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 4,
                'period' => '2024-01',
                'amount' => 600000.00,
                'due_date' => '2024-01-15',
                'status' => InvoiceStatus::UNPAID->value,
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => 5,
                'period' => '2024-01',
                'amount' => 550000.00,
                'due_date' => '2024-01-15',
                'status' => InvoiceStatus::UNPAID->value,
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}