<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Enums\CleaningScheduleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CleaningReviewController extends Controller
{
    


public function index()
{
    $schedules = CleaningSchedule::with(['tenant.room', 'room'])
        ->whereIn('status', [
            CleaningScheduleStatus::PENDING->value,
            CleaningScheduleStatus::APPROVED->value,
            CleaningScheduleStatus::RESCHEDULED->value,
        ])
        ->orderByDesc('created_at')
        ->paginate(10);

    $totalPending = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::PENDING->value
    )->count();

    $totalApproved = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::APPROVED->value
    )->count();

    $totalRescheduled = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::RESCHEDULED->value
    )->count();

    $stats = [
        'pending' => $totalPending,
        'approved' => $totalApproved,
        'rescheduled' => $totalRescheduled,
    ];

    return view('pengurus.cleaning.index', compact(
        'schedules',
        'totalPending',
        'totalApproved',
        'totalRescheduled',
        'stats'
    ));
}

public function report()
{
    $schedules = CleaningSchedule::with(['tenant.room', 'room'])
        ->whereIn('status', [
            CleaningScheduleStatus::DONE->value,
            CleaningScheduleStatus::REJECTED->value,
        ])
        ->latest()
        ->paginate(10);

    $totalReports = CleaningSchedule::count();

    $totalDone = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::DONE->value
    )->count();

    $totalPending = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::PENDING->value
    )->count();

    $totalRescheduled = CleaningSchedule::where(
        'status',
        CleaningScheduleStatus::RESCHEDULED->value
    )->count();

    $stats = [
        'total' => $totalReports,
        'done' => $totalDone,
        'pending' => $totalPending,
        'rescheduled' => $totalRescheduled,
    ];

    return view('pengurus.cleaning.report', compact(
        'schedules',
        'stats'
    ));
}


    
public function update(Request $request, CleaningSchedule $schedule)
{
    $validated = $request->validate([
        'status' => ['required', 'in:approved,rejected,rescheduled,done'],
        'rescheduled_date' => [
            'required_if:status,rescheduled',
            'nullable',
            'date',
            'after:today'
        ],
        'rescheduled_time' => [
            'required_if:status,rescheduled',
            'nullable',
            'date_format:H:i'
        ],
        'rejection_reason' => [
            'required_if:status,rejected',
            'nullable',
            'string',
            'max:1000'
        ],
        'note' => ['nullable', 'string'],
    ], [
        'rejection_reason.required_if' => 'Alasan penolakan wajib diisi.',
        'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
    ]);

    try {
        DB::transaction(function () use ($validated, $schedule) {

            DB::table('users')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            $schedule->refresh();

            if ($schedule->status !== CleaningScheduleStatus::PENDING) {
                throw new \RuntimeException(
                    'Permintaan ini sudah diproses oleh pengurus lain.'
                );
            }

            $targetDate = $validated['status'] === 'rescheduled'
                ? $validated['rescheduled_date']
                : $schedule->requested_date->format('Y-m-d');

            $targetTime = $validated['status'] === 'rescheduled'
                ? $validated['rescheduled_time']
                : substr($schedule->requested_time, 0, 5);

            if (in_array($validated['status'], ['approved', 'rescheduled'])) {

                $conflict = CleaningSchedule::where('id', '!=', $schedule->id)
                    ->whereIn('status', ['approved', 'rescheduled'])
                    ->where(function ($query) use ($targetDate, $targetTime) {

                        $query->where(function ($q) use ($targetDate, $targetTime) {
                            $q->whereDate('requested_date', $targetDate)
                                ->whereTime('requested_time', $targetTime);
                        });

                        $query->orWhere(function ($q) use ($targetDate, $targetTime) {
                            $q->whereDate('rescheduled_date', $targetDate)
                                ->whereTime('rescheduled_time', $targetTime);
                        });
                    })
                    ->exists();

                if ($conflict) {
                    throw new \RuntimeException(
                        'Jadwal tersebut sudah diterima untuk penghuni lain. Silakan pilih waktu lain.'
                    );
                }
            }

            $schedule->update([
                'status' => $validated['status'],

                'rescheduled_date' => $validated['status'] === 'rescheduled'
                    ? $validated['rescheduled_date']
                    : null,

                'rescheduled_time' => $validated['status'] === 'rescheduled'
                    ? $validated['rescheduled_time']
                    : null,

                'rejection_reason' => $validated['status'] === 'rejected'
                    ? $validated['rejection_reason']
                    : null,

                'note' => $validated['note'] ?? $schedule->note,
                'reviewed_by' => Auth::id(),
            ]);
        });

    } catch (\RuntimeException $e) {
        return redirect()
            ->route('pengurus.cleaning.index')
            ->with('error', $e->getMessage());
    }

    return redirect()
        ->route('pengurus.cleaning.index')
        ->with('success', 'Jadwal kebersihan berhasil diverifikasi.');
}


    public function markAsDone(CleaningSchedule $schedule)
    {
        if ($schedule->status !== CleaningScheduleStatus::APPROVED) {
            return redirect()
                ->route('pengurus.cleaning.index')
                ->with(
                    'error',
                    'Hanya jadwal yang sudah disetujui yang dapat ditandai selesai.'
                );
        }

        $schedule->update([
            'status' => CleaningScheduleStatus::DONE,
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()
            ->route('pengurus.cleaning.index')
            ->with(
                'success',
                'Jadwal kebersihan berhasil ditandai sebagai selesai.'
            );
    }
}
