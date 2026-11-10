<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Enums\DamageReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    public function index()
    {
        $reports = DamageReport::with('tenant', 'room')->latest()->paginate(10);
        return view('pengurus.damage_reports.index', compact('reports'));
    }

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

        return redirect()->route('pengurus.damage_reports.index')->with('success', 'Laporan kerusakan berhasil diperbarui.');
    }
}
