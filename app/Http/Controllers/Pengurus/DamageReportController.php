<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Enums\DamageReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    // Menampilkan laporan kerusakan yang masih aktif
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'active');
        $search = $request->query('search');

        $baseQuery = DamageReport::with(['tenant', 'room']);

        if ($statusFilter === 'open') {
            $baseQuery->where('status', DamageReportStatus::OPEN->value);
        } elseif ($statusFilter === 'in_progress') {
            $baseQuery->where('status', DamageReportStatus::IN_PROGRESS->value);
        } else {
            // Default: semua laporan aktif (open & in_progress)
            $baseQuery->whereIn('status', [
                DamageReportStatus::OPEN->value,
                DamageReportStatus::IN_PROGRESS->value,
            ]);
        }

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('shared_area_detail', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $baseQuery->latest()->paginate(10)->withQueryString();

        // Hitung statistik untuk badge/filter tab
        $openCount = DamageReport::where('status', DamageReportStatus::OPEN->value)->count();
        $inProgressCount = DamageReport::where('status', DamageReportStatus::IN_PROGRESS->value)->count();
        $activeTotal = $openCount + $inProgressCount;

        return view('pengurus.damage_reports.index', compact(
            'reports',
            'statusFilter',
            'search',
            'openCount',
            'inProgressCount',
            'activeTotal'
        ));
    }

    // Menampilkan riwayat laporan yang sudah selesai
    public function history(Request $request)
    {
        $search = $request->query('search');

        $baseQuery = DamageReport::with(['tenant', 'room', 'handler'])
            ->where('status', DamageReportStatus::RESOLVED->value);

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('shared_area_detail', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('full_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('handler', function ($hq) use ($search) {
                      $hq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $baseQuery->latest('resolved_at')->paginate(10)->withQueryString();

        return view('pengurus.damage_reports.history', compact('reports', 'search'));
    }

    // Memperbarui status laporan kerusakan
    public function update(Request $request, DamageReport $report)
    {
        // Cegah perubahan bila laporan dibatalkan oleh penghuni
        if ($report->status === DamageReportStatus::CANCELLED || $report->status?->value === 'cancelled') {
            return redirect()
                ->route('pengurus.damage_reports.index')
                ->with('error', 'Laporan ini telah dibatalkan oleh penghuni dan tidak dapat diproses.');
        }

        // Cegah perubahan bila laporan sudah selesai
        if ($report->status === DamageReportStatus::RESOLVED || $report->status?->value === 'resolved') {
            return redirect()
                ->route('pengurus.damage_reports.index')
                ->with('error', 'Laporan ini sudah berstatus selesai.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:in_progress,resolved'],
        ]);

        $newStatus = $validated['status'];

        $updateData = [
            'status' => $newStatus,
            'handled_by' => Auth::id(),
        ];

        if ($newStatus === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $report->update($updateData);

        $message = $newStatus === 'in_progress' 
            ? 'Laporan kerusakan berhasil diproses. Status kini sedang ditangani.'
            : 'Laporan kerusakan telah berhasil ditandai selesai.';

        return redirect()
            ->route('pengurus.damage_reports.index')
            ->with('success', $message);
    }
}
 