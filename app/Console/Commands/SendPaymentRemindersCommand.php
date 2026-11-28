<?php

namespace App\Console\Commands;

use App\Services\TelegramReminderService;
use Illuminate\Console\Command;

class SendPaymentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     */
    protected $description = 'Kirim pengingat iuran otomatis (H-7, H-1, Hari H) melalui Telegram kepada penghuni yang belum lunas.';

    /**
     * Execute the console command.
     */
    public function handle(TelegramReminderService $service): int
    {
        $this->info('Memulai pengecekan dan pengiriman pengingat iuran otomatis...');

        $results = $service->sendScheduledReminders();

        $this->table(
            ['Tipe Jadwal', 'Berhasil Dikirim', 'Gagal', 'Dilewati'],
            [
                ['H-7 Jatuh Tempo', $results['h_minus_7']['sent'], $results['h_minus_7']['failed'], $results['h_minus_7']['skipped']],
                ['H-1 Jatuh Tempo', $results['h_minus_1']['sent'], $results['h_minus_1']['failed'], $results['h_minus_1']['skipped']],
                ['Hari H Jatuh Tempo', $results['h_day']['sent'], $results['h_day']['failed'], $results['h_day']['skipped']],
            ]
        );

        $this->info('Selesai memproses pengingat iuran.');

        return Command::SUCCESS;
    }
}
