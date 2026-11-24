<?php
namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\Role;
use App\Models\Room;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoomSeeder::class,
            TenantSeeder::class,
            InvoiceSeeder::class,
        ]);

        Room::all()->each->syncOccupancyStatus();
    }
}
