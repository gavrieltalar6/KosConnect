<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\CleaningScheduleStatus;
use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use Illuminate\Http\Request;

class CleaningReportController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search');

        $query = CleaningSchedule::with(['tenant.room', 'room', 'reviewer']);

        if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'approved', 'done', 'rejected'])) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tenant.room', function ($trq) use ($search) {
                      $trq->where('room_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('reviewer', function ($hq) use ($search) {
                      $hq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $schedules = $query->latest()->paginate(10)->withQueryString();

        $totalReports = CleaningSchedule::count();
        $pendingCount = CleaningSchedule::where('status', CleaningScheduleStatus::PENDING->value)->count();
        $approvedCount = CleaningSchedule::where('status', CleaningScheduleStatus::APPROVED->value)->count();
        $doneCount = CleaningSchedule::where('status', CleaningScheduleStatus::DONE->value)->count();
        $rejectedCount = CleaningSchedule::where('status', CleaningScheduleStatus::REJECTED->value)->count();

        return view('pemilik.cleaning.index', compact(
            'schedules',
            'statusFilter',
            'search',
            'totalReports',
            'pendingCount',
            'approvedCount',
            'doneCount',
            'rejectedCount'
        ));
    }
}
