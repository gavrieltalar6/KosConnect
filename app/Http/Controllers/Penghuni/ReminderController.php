<?php

namespace App\Http\Controllers\Penghuni;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReminderController extends Controller
{
    /**
     * Halaman Pengingat Iuran khusus untuk Penghuni.
     */
    public function index(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return redirect()->route('penghuni.dashboard')
                ->with('error', 'Profil penghuni Anda belum terdaftar.');
        }

        // Pastikan memiliki token penghubung telegram
        if (!$tenant->telegram_link_token) {
            $tenant->telegram_link_token = Str::random(24);
            $tenant->save();
        }

        // Ambil daftar tagihan penghuni beserta reminder logs
        $invoices = Invoice::with(['room', 'reminderLogs'])
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('due_date')
            ->paginate(10);

        // Tagihan belum lunas terdekat
        $nearestInvoice = Invoice::with(['room', 'reminderLogs'])
            ->where('tenant_id', $tenant->id)
            ->where('status', InvoiceStatus::UNPAID)
            ->orderBy('due_date', 'asc')
            ->first();

        // 3 Box status untuk tagihan terdekat
        $targetInvoice = $nearestInvoice ?? $invoices->first();
        $boxH7 = $targetInvoice ? $targetInvoice->getReminderBoxState('h_minus_7') : 'pending';
        $boxH1 = $targetInvoice ? $targetInvoice->getReminderBoxState('h_minus_1') : 'pending';
        $boxHDay = $targetInvoice ? $targetInvoice->getReminderBoxState('h_day') : 'pending';

        $botUsername = config('services.telegram.bot_username');
        $telegramLink = $botUsername 
            ? "https://t.me/{$botUsername}?start={$tenant->telegram_link_token}" 
            : null;

        return view('penghuni.reminders.index', compact(
            'tenant',
            'invoices',
            'nearestInvoice',
            'targetInvoice',
            'boxH7',
            'boxH1',
            'boxHDay',
            'botUsername',
            'telegramLink'
        ));
    }

    /**
     * Toggle saklar on/off pengingat iuran oleh penghuni sendiri.
     */
    public function toggle(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $tenant->is_reminder_active = !$tenant->is_reminder_active;
        $tenant->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $tenant->is_reminder_active,
                'message' => 'Pengingat iuran otomatis berhasil di' . ($tenant->is_reminder_active ? 'aktifkan' : 'nonaktifkan') . '.',
            ]);
        }

        return redirect()->back()->with(
            'success',
            'Pengingat iuran otomatis berhasil di' . ($tenant->is_reminder_active ? 'aktifkan' : 'nonaktifkan') . '.'
        );
    }
}
