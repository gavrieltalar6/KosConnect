<?php

namespace App\Console;

use Illuminate\Support\Facades\Schedule;
use App\Services\TelegramReminderService;

// Menjalankan pengingat iuran otomatis (H-7, H-1, Hari H) setiap hari pukul 08:00
Schedule::call(function () {
    app(TelegramReminderService::class)->sendScheduledReminders();
})->dailyAt('08:00');
