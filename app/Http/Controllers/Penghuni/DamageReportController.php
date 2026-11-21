<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Enums\DamageReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DamageReportController extends Controller
{
    public function index(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            $reports = DamageReport::whereRaw('1 = 0')->paginate(10);
            return view('penghuni.damage_reports.index', compact('reports', 'tenant'))
                ->with('error', 'Profil penghuni Anda belum terdaftar.');
        }

        $reports = DamageReport::with(['room', 'handler'])
            ->where('tenant_id', $tenant->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penghuni.damage_reports.index', compact('reports', 'tenant'));
    }

    public function create()
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return redirect()->route('penghuni.damage_reports.index')
                ->with('error', 'Profil penghuni Anda belum terdaftar.');
        }

        if (!$tenant->room_id || !$tenant->room) {
            return redirect()->route('penghuni.damage_reports.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif. Harap hubungi pengurus atau pemilik kos sebelum membuat laporan kerusakan.');
        }

        $room = $tenant->room;

        return view('penghuni.damage_reports.create', compact('tenant', 'room'));
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant || !$tenant->room_id) {
            return redirect()->route('penghuni.damage_reports.index')
                ->with('error', 'Anda belum memiliki kamar terdaftar untuk mengajukan laporan kerusakan.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ], [
            'title.required' => 'Judul laporan kerusakan wajib diisi.',
            'title.max' => 'Judul laporan kerusakan maksimal 255 karakter.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'description.max' => 'Deskripsi kerusakan maksimal 2000 karakter.',
        ]);

        DamageReport::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => DamageReportStatus::OPEN,
        ]);

        return redirect()->route('penghuni.damage_reports.index')
            ->with('success', 'Laporan kerusakan berhasil dikirim. Pengurus akan segera memverifikasi dan menindaklanjuti.');
    }

    public function cancel(DamageReport $report)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant || $report->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan laporan ini.');
        }

        if ($report->status === DamageReportStatus::OPEN || $report->status?->value === 'open') {
            $report->update([
                'status' => DamageReportStatus::CANCELLED,
            ]);

            return redirect()->route('penghuni.damage_reports.index')
                ->with('success', 'Laporan kerusakan berhasil dibatalkan.');
        }

        return redirect()->route('penghuni.damage_reports.index')
            ->with('error', 'Laporan tidak dapat dibatalkan karena sudah dalam penanganan atau telah selesai.');
    }
}
