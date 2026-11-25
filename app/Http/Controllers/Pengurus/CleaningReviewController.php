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
    $schedules = CleaningSchedule::with(['tenant', 'room'])
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
    $schedules = CleaningSchedule::with(['tenant', 'room'])
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
            'status' => [
                'required',
                'in:approved,rejected,rescheduled,done'
            ],
            'rescheduled_date' => [
                'required_if:status,rescheduled',
                'nullable',
                'date'
            ],
            'rescheduled_time' => [
                'required_if:status,rescheduled',
                'nullable',
                'date_format:H:i'
            ],
            'note' => ['nullable', 'string'],
        ]);

        // PROSES PERSETUJUAN
        if ($validated['status'] === 'approved') {

            $result = DB::transaction(function () use ($schedule) {

                // Kunci permintaan pada tanggal dan jam yang sama.
                $slotRequests = CleaningSchedule::where(function ($query) use ($schedule) {

                    $query->where(function ($q) use ($schedule) {
                        $q->whereDate(
                            'requested_date',
                            $schedule->requested_date->format('Y-m-d')
                        )->whereTime(
                            'requested_time',
                            $schedule->requested_time
                        );
                    });

                    $query->orWhere(function ($q) use ($schedule) {
                        $q->whereDate(
                            'rescheduled_date',
                            $schedule->requested_date->format('Y-m-d')
                        )->whereTime(
                            'rescheduled_time',
                            $schedule->requested_time
                        );
                    });

                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

                $current = $slotRequests->firstWhere('id', $schedule->id);

                if (
                    !$current ||
                    $current->status !== CleaningScheduleStatus::PENDING
                ) {
                    return 'processed';
                }

                // Periksa apakah jadwal sudah dimiliki penghuni lain.
                $slotAlreadyTaken = $slotRequests->contains(
                    function ($item) use ($current) {

                        if ($item->id === $current->id) {
                            return false;
                        }

                        if ($item->status === CleaningScheduleStatus::APPROVED) {
                            return
                                $item->requested_date->format('Y-m-d')
                                === $current->requested_date->format('Y-m-d')
                                &&
                                substr((string) $item->requested_time, 0, 5)
                                === substr((string) $current->requested_time, 0, 5);
                        }

                        if ($item->status === CleaningScheduleStatus::RESCHEDULED) {
                            return
                                $item->rescheduled_date?->format('Y-m-d')
                                === $current->requested_date->format('Y-m-d')
                                &&
                                substr((string) $item->rescheduled_time, 0, 5)
                                === substr((string) $current->requested_time, 0, 5);
                        }

                        return false;
                    }
                );

                if ($slotAlreadyTaken) {
                    return 'taken';
                }

                // Setujui permintaan pertama.
                $current->update([
                    'status' => CleaningScheduleStatus::APPROVED,
                    'reviewed_by' => Auth::id(),
                ]);

                return 'approved';

            }, 3);

            if ($result === 'taken') {
                return redirect()
                    ->route('pengurus.cleaning.index')
                    ->with(
                        'error',
                        'Jadwal tersebut sudah disetujui untuk penghuni lain. Permintaan ini tidak dapat disetujui.'
                    );
            }

            if ($result === 'processed') {
                return redirect()
                    ->route('pengurus.cleaning.index')
                    ->with(
                        'error',
                        'Permintaan ini sudah diproses oleh pengurus lain.'
                    );
            }

            return redirect()
                ->route('pengurus.cleaning.index')
                ->with(
                    'success',
                    'Permintaan pembersihan berhasil disetujui.'
                );
        }

        // PROSES PENOLAKAN ATAU PERUBAHAN JADWAL
        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()
                ->route('pengurus.cleaning.index')
                ->with(
                    'error',
                    'Permintaan ini sudah diproses dan tidak dapat diubah lagi.'
                );
        }

        $schedule->update([
            'status' => $validated['status'],
            'rescheduled_date' => $validated['rescheduled_date'] ?? null,
            'rescheduled_time' => $validated['rescheduled_time'] ?? null,
            'note' => $validated['note'] ?? $schedule->note,
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()
            ->route('pengurus.cleaning.index')
            ->with(
                'success',
                'Permintaan pembersihan berhasil ditolak.'
            );
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
