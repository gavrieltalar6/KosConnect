<?php

namespace App\Http\Controllers\Penghuni;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CleaningSchedule;
use App\Enums\CleaningScheduleStatus;
use App\Enums\PresenceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CleaningScheduleController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        // Periksa apakah penghuni memiliki kamar aktif
        $hasActiveRoom = $tenant && !empty($tenant->room_id);

        if ($hasActiveRoom) {
            $schedules = CleaningSchedule::where('tenant_id', $tenant->id)
                ->whereIn('status', [
                    CleaningScheduleStatus::PENDING->value,
                    CleaningScheduleStatus::APPROVED->value,
                    CleaningScheduleStatus::RESCHEDULED->value,
                ])
                ->orderByDesc('created_at')
                ->paginate(10);
        } else {
            $schedules = collect();
        }

        return view('penghuni.cleaning.index', compact(
            'schedules',
            'hasActiveRoom'
        ));
    }

    public function report()
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return redirect()->route('penghuni.dashboard')
                ->with('error', 'Akun ini belum terhubung dengan data penghuni.');
        }

        $schedules = CleaningSchedule::where('tenant_id', $tenant->id)
            ->whereIn('status', [
                CleaningScheduleStatus::DONE->value,
                CleaningScheduleStatus::REJECTED->value,
            ])
            ->latest()
            ->paginate(10);

        return view('penghuni.cleaning.report', compact('schedules'));
    }

    public function create()
    {
        $tenant = Auth::user()->tenant;

        // Cegah penghuni tanpa kamar membuka form pengajuan
        if (!$tenant || empty($tenant->room_id)) {
            return redirect()
                ->route('penghuni.cleaning.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif. Harap hubungi pengurus atau pemilik kos sebelum membuat permintaan pembersihan kamar.');
        }

        return view('penghuni.cleaning.create');
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        // Validasi kamar dilakukan sebelum menyimpan pengajuan
        if (!$tenant || empty($tenant->room_id)) {
            return redirect()
                ->route('penghuni.cleaning.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif. Harap hubungi pengurus atau pemilik kos sebelum membuat permintaan pembersihan kamar.');
        }

        $validated = $request->validate([
            'requested_date' => ['required', 'date', 'after_or_equal:today'],
            'requested_time' => ['required', 'date_format:H:i'],
            'presence_status' => ['required', 'in:occupied,vacant'],
            'note' => ['nullable', 'string'],
        ], [
            'requested_date.after_or_equal' => 'Tanggal pengajuan tidak boleh sebelum hari ini.',
            'requested_date.required' => 'Tanggal pembersihan wajib diisi.',
            'requested_time.required' => 'Waktu pembersihan wajib diisi.',
            'presence_status.required' => 'Status keberadaan wajib dipilih.',
        ]);

        // Periksa apakah jadwal sudah digunakan penghuni lain
        $scheduleExists = CleaningSchedule::whereDate(
            'requested_date',
            $validated['requested_date']
        )
            ->whereTime('requested_time', $validated['requested_time'])
            ->whereIn('status', [
                CleaningScheduleStatus::PENDING->value,
                CleaningScheduleStatus::APPROVED->value,
                CleaningScheduleStatus::RESCHEDULED->value,
            ])
            ->exists();

        if ($scheduleExists) {
            return back()->withInput()
                ->with('error', 'Pengajuan gagal! Jadwal pada tanggal dan jam tersebut sudah diambil oleh penghuni lain. Silakan pilih jam yang berbeda.');
        }

        CleaningSchedule::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'requested_date' => $validated['requested_date'],
            'requested_time' => $validated['requested_time'],
            'presence_status' => $validated['presence_status'],
            'status' => CleaningScheduleStatus::PENDING->value,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('penghuni.cleaning.index')
            ->with('success', 'Pengajuan pembersihan kamar berhasil dikirim.');
    }

    public function edit(CleaningSchedule $schedule)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant || empty($tenant->room_id)) {
            return redirect()
                ->route('penghuni.cleaning.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif.');
        }

        if ($schedule->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')
                ->with('error', 'Jadwal yang sudah diproses tidak dapat diubah.');
        }

        return view('penghuni.cleaning.edit', compact('schedule'));
    }

    public function update(Request $request, CleaningSchedule $schedule)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant || empty($tenant->room_id)) {
            return redirect()
                ->route('penghuni.cleaning.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif.');
        }

        if ($schedule->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')
                ->with('error', 'Jadwal yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'requested_date' => ['required', 'date', 'after:today'],
            'requested_time' => ['required', 'date_format:H:i'],
            'presence_status' => ['required', 'in:occupied,vacant'],
            'note' => ['nullable', 'string'],
        ]);

        $schedule->update([
            'requested_date' => $validated['requested_date'],
            'requested_time' => $validated['requested_time'],
            'presence_status' => PresenceStatus::from($validated['presence_status']),
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('penghuni.cleaning.index')
            ->with('success', 'Jadwal kebersihan berhasil diperbarui.');
    }

    public function destroy(CleaningSchedule $schedule)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant || empty($tenant->room_id)) {
            return redirect()
                ->route('penghuni.cleaning.index')
                ->with('error', 'Anda belum terdaftar pada kamar aktif.');
        }

        if ($schedule->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($schedule->status !== CleaningScheduleStatus::PENDING) {
            return redirect()->route('penghuni.cleaning.index')
                ->with('error', 'Jadwal yang sudah diproses tidak dapat dihapus.');
        }

        $schedule->delete();

        return redirect()->route('penghuni.cleaning.index')
            ->with('success', 'Jadwal kebersihan berhasil dibatalkan.');
    }
}
