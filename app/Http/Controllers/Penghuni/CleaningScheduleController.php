<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Models\Tenant;
use App\Enums\CleaningScheduleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleaningScheduleController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $schedules = CleaningSchedule::where('tenant_id', $tenant->id)
            ->whereIn('status', [CleaningScheduleStatus::PENDING, CleaningScheduleStatus::APPROVED, CleaningScheduleStatus::RESCHEDULED])
            ->latest()
            ->paginate(10);
        return view('penghuni.cleaning.index', compact('schedules'));
    }

    public function report()
    {
        $tenant = Auth::user()->tenant;
        $schedules = CleaningSchedule::where('tenant_id', $tenant->id)
            ->whereIn('status', [CleaningScheduleStatus::DONE, CleaningScheduleStatus::REJECTED])
            ->latest()
            ->paginate(10);
        return view('penghuni.cleaning.report', compact('schedules'));
    }

    public function create()
    {
        return view('penghuni.cleaning.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_date' => ['required', 'date', 'after:today'],
            'requested_time' => ['required', 'date_format:H:i'],
            'presence_status' => ['required', 'in:occupied,vacant'],
            'note' => ['nullable', 'string'],
        ]);

        $tenant = Auth::user()->tenant;

        CleaningSchedule::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'status' => CleaningScheduleStatus::PENDING,
        ]));

        return redirect()->route('penghuni.cleaning.index')->with('success', 'Jadwal kebersihan berhasil diajukan.');
    }

    public function edit(CleaningSchedule $schedule)
    {
        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')->with('error', 'Jadwal yang sudah diproses tidak dapat diubah.');
        }

        return view('penghuni.cleaning.edit', compact('schedule'));
    }

    public function update(Request $request, CleaningSchedule $schedule)
    {
        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')->with('error', 'Jadwal yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'requested_date' => ['required', 'date', 'after:today'],
            'requested_time' => ['required', 'date_format:H:i'],
            'presence_status' => ['required', 'in:occupied,vacant'],
            'note' => ['nullable', 'string'],
        ]);

        $schedule->update($validated);

        return redirect()->route('penghuni.cleaning.index')->with('success', 'Jadwal kebersihan berhasil diperbarui.');
    }

    public function destroy(CleaningSchedule $schedule)
    {
        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')->with('error', 'Jadwal yang sudah diproses tidak dapat dihapus.');
        }

        $schedule->delete();

        return redirect()->route('penghuni.cleaning.index')->with('success', 'Jadwal kebersihan berhasil dibatalkan.');
    }
}
