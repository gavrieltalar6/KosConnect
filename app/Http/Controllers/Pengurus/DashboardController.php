<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Models\DamageReport;
use App\Models\Invoice;
use App\Enums\CleaningScheduleStatus;
use App\Enums\DamageReportStatus;
use App\Enums\InvoiceStatus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // 1. Jumlah jadwal kebersihan aktif
        $activeCleaningSchedules = CleaningSchedule::whereIn('status', [
            CleaningScheduleStatus::PENDING->value,
            CleaningScheduleStatus::APPROVED->value,
        ])->count();

        // 2. Jumlah laporan kerusakan aktif
        $activeDamageReports = DamageReport::whereIn('status', [
            DamageReportStatus::OPEN->value,
            DamageReportStatus::IN_PROGRESS->value,
        ])->count();

        // 3. Jumlah tagihan yang mendekati jatuh tempo (7 hari)
        $upcomingDueInvoices = Invoice::where('status', InvoiceStatus::UNPAID->value)
            ->whereBetween('due_date', [
                today()->toDateString(),
                today()->addDays(7)->toDateString(),
            ])
            ->count();

        // 4. Jumlah penghuni unik yang terlambat membayar
        $overdueTenants = Invoice::where('status', InvoiceStatus::UNPAID->value)
            ->whereDate('due_date', '<', today())
            ->distinct('tenant_id')
            ->count('tenant_id');

        return view('pengurus.dashboard', compact(
            'activeCleaningSchedules',
            'activeDamageReports',
            'upcomingDueInvoices',
            'overdueTenants'
        ));
    }
}
