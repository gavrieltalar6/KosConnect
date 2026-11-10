<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Enums\CleaningScheduleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CleaningReviewController extends Controller
{
    public function index()
    {
        $schedules = CleaningSchedule::with('tenant')
            ->whereIn('status', [CleaningScheduleStatus::PENDING, CleaningScheduleStatus::APPROVED, CleaningScheduleStatus::RESCHEDULED])
            ->latest()
            ->paginate(10);
        return view('pengurus.cleaning.index', compact('schedules'));
    }

    public function report()
    {
        $schedules = CleaningSchedule::with('tenant')
            ->whereIn('status', [CleaningScheduleStatus::DONE, CleaningScheduleStatus::REJECTED])
            ->latest()
            ->paginate(10);
        return view('pengurus.cleaning.report', compact('schedules'));
    }

    public function update(Request $request, CleaningSchedule $schedule)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,rescheduled,done'],
            'rescheduled_date' => ['required_if:status,rescheduled', 'nullable', 'date'],
            'rescheduled_time' => ['required_if:status,rescheduled', 'nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string'],
        ]);

        $schedule->update([
            'status' => $validated['status'],
            'rescheduled_date' => $validated['rescheduled_date'] ?? null,
            'rescheduled_time' => $validated['rescheduled_time'] ?? null,
            'note' => $validated['note'] ?? $schedule->note,
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()->route('pengurus.cleaning.index')->with('success', 'Jadwal kebersihan berhasil diverifikasi.');
    }

    public function markAsDone(CleaningSchedule $schedule)
    {
        $schedule->update([
            'status' => CleaningScheduleStatus::DONE,
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()->route('pengurus.cleaning.index')->with('success', 'Jadwal kebersihan berhasil ditandai sebagai selesai.');
    }
}
