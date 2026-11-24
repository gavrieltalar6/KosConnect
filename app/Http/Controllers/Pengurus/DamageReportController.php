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
    public function index()
    {
        $reports = DamageReport::with('tenant', 'room')
            ->whereIn('status', [
                DamageReportStatus::OPEN->value,
                DamageReportStatus::IN_PROGRESS->value,
            ])
            ->latest()
            ->paginate(10);

        return view('pengurus.damage_reports.index', compact('reports'));
    }

    // Menampilkan riwayat laporan yang sudah selesai
    public function history()
    {
        $reports = DamageReport::with('tenant', 'room', 'handler')
            ->where('status', DamageReportStatus::RESOLVED->value)
            ->latest()
            ->paginate(10);

        return view('pengurus.damage_reports.history', compact('reports'));
    }

    // Memperbarui status laporan kerusakan
    public function update(Request $request, DamageReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:in_progress,resolved'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'handled_by' => Auth::id(),
            'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
        ]);

        return redirect()
            ->route('pengurus.damage_reports.index')
            ->with('success', 'Laporan kerusakan berhasil diperbarui.');
    }
}
 