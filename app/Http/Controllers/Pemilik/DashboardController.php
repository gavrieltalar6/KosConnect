<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\DamageReport;
use App\Enums\RoomStatus;
use App\Enums\TenantStatus;
use App\Enums\InvoiceStatus;
use App\Enums\DamageReportStatus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', RoomStatus::OCCUPIED->value)->count();
        $availableRooms = Room::where('status', RoomStatus::AVAILABLE->value)->count();
        $activeTenants = Tenant::where('status', TenantStatus::ACTIVE->value)->count();
        $unpaidInvoices = Invoice::where('status', InvoiceStatus::UNPAID->value)->count();

        $activeDamageReports = DamageReport::whereIn('status', [
            DamageReportStatus::OPEN->value,
            DamageReportStatus::IN_PROGRESS->value,
        ])->count();

        $recentDamageReports = DamageReport::with(['tenant', 'room'])
            ->latest()
            ->take(5)
            ->get();

        return view('pemilik.dashboard', compact(
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'activeTenants',
            'unpaidInvoices',
            'activeDamageReports',
            'recentDamageReports'
        ));
    }
}
