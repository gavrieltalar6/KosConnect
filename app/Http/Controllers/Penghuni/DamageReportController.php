<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Enums\DamageReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $reports = DamageReport::where('tenant_id', $tenant->id)->latest()->paginate(10);
        return view('penghuni.damage_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('penghuni.damage_reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $tenant = Auth::user()->tenant;

        DamageReport::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => DamageReportStatus::OPEN,
        ]);

        return redirect()->route('penghuni.damage_reports.index')->with('success', 'Laporan kerusakan berhasil dikirim.');
    }

    public function cancel(DamageReport $report)
    {
        $tenant = Auth::user()->tenant;

        if ($report->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($report->status === DamageReportStatus::OPEN) {
            $report->update(['status' => DamageReportStatus::CANCELLED]);
            return redirect()->route('penghuni.damage_reports.index')->with('success', 'Laporan kerusakan berhasil dibatalkan.');
        }

        return redirect()->route('penghuni.damage_reports.index')->with('error', 'Laporan tidak dapat dibatalkan.');
    }
}
