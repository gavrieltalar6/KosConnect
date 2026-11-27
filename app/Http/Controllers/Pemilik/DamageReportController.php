<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Enums\DamageReportStatus;
use Illuminate\Http\Request;

class DamageReportController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search');

        $query = DamageReport::with(['tenant', 'room', 'handler']);

        if ($statusFilter !== 'all' && in_array($statusFilter, ['open', 'in_progress', 'resolved', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('shared_area_detail', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('handler', function ($hq) use ($search) {
                      $hq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        // Metrics
        $totalReports = DamageReport::count();
        $openCount = DamageReport::where('status', DamageReportStatus::OPEN->value)->count();
        $inProgressCount = DamageReport::where('status', DamageReportStatus::IN_PROGRESS->value)->count();
        $resolvedCount = DamageReport::where('status', DamageReportStatus::RESOLVED->value)->count();

        return view('pemilik.damage_reports.index', compact(
            'reports',
            'statusFilter',
            'search',
            'totalReports',
            'openCount',
            'inProgressCount',
            'resolvedCount'
        ));
    }
}
