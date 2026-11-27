<?php

namespace App\Http\Controllers\Pengurus;

use App\Enums\InvoiceStatus;
use App\Enums\ReminderType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Services\TelegramReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * Menampilkan daftar pengingat iuran untuk seluruh penghuni.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $filterStatus = $request->query('reminder_status', 'all');

        $query = Tenant::with([
            'room',
            'invoices' => function ($q) {
                $q->with('reminderLogs')->orderBy('due_date', 'asc');
            },
        ])->where('status', 'active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterStatus === 'active') {
            $query->where('is_reminder_active', true);
        } elseif ($filterStatus === 'inactive') {
            $query->where('is_reminder_active', false);
        }

        $tenants = $query->orderBy('full_name', 'asc')->paginate(10)->withQueryString();

        // Siapkan data pengingat per penghuni
        $tenantReminderData = $tenants->getCollection()->map(function (Tenant $tenant) {
            // Ambil tagihan belum lunas terdekat
            $nearestInvoice = $tenant->invoices
                ->where('status', InvoiceStatus::UNPAID)
                ->sortBy('due_date')
                ->first();

            // Jika semua lunas, ambil tagihan terakhir untuk melihat riwayat
            $latestInvoice = $nearestInvoice ?? $tenant->invoices->sortByDesc('due_date')->first();

            // Status 3 Box: H-7, H-1, Hari H
            $h7State = $latestInvoice ? $latestInvoice->getReminderBoxState('h_minus_7') : 'pending';
            $h1State = $latestInvoice ? $latestInvoice->getReminderBoxState('h_minus_1') : 'pending';
            $hDayState = $latestInvoice ? $latestInvoice->getReminderBoxState('h_day') : 'pending';

            return [
                'tenant' => $tenant,
                'is_active' => (bool) $tenant->is_reminder_active,
                'invoice' => $nearestInvoice,
                'latest_invoice' => $latestInvoice,
                'due_date' => $nearestInvoice ? $nearestInvoice->due_date : ($latestInvoice ? $latestInvoice->due_date : null),
                'box_h7' => $h7State,
                'box_h1' => $h1State,
                'box_hday' => $hDayState,
                'log_h7' => $latestInvoice ? $latestInvoice->getReminderLog('h_minus_7') : null,
                'log_h1' => $latestInvoice ? $latestInvoice->getReminderLog('h_minus_1') : null,
                'log_hday' => $latestInvoice ? $latestInvoice->getReminderLog('h_day') : null,
            ];
        });

        // Overview Metric Cards
        $totalTenants = Tenant::where('status', 'active')->count();
        $activeReminders = Tenant::where('status', 'active')->where('is_reminder_active', true)->count();
        $unpaidInvoicesCount = Invoice::where('status', InvoiceStatus::UNPAID)->count();
        $telegramLinkedCount = Tenant::where('status', 'active')->whereNotNull('telegram_chat_id')->count();

        $botUsername = config('services.telegram.bot_username');

        return view('pengurus.reminders.index', compact(
            'tenants',
            'tenantReminderData',
            'search',
            'filterStatus',
            'totalTenants',
            'activeReminders',
            'unpaidInvoicesCount',
            'telegramLinkedCount',
            'botUsername'
        ));
    }

    /**
     * Mengubah saklar ON / OFF autoreminder untuk penghuni tertentu.
     */
    public function toggle(Request $request, Tenant $tenant)
    {
        $tenant->is_reminder_active = !$tenant->is_reminder_active;
        $tenant->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $tenant->is_reminder_active,
                'message' => 'Pengingat iuran untuk ' . $tenant->full_name . ' berhasil di' . ($tenant->is_reminder_active ? 'aktifkan' : 'nonaktifkan') . '.',
            ]);
        }

        return redirect()->back()->with(
            'success',
            'Pengingat iuran untuk ' . $tenant->full_name . ' berhasil di' . ($tenant->is_reminder_active ? 'aktifkan' : 'nonaktifkan') . '.'
        );
    }

    /**
     * Kirim pengingat manual sekarang juga untuk tagihan belum lunas terdekat.
     */
    public function sendManual(Request $request, Tenant $tenant, TelegramReminderService $reminderService)
    {
        $invoice = $tenant->invoices()
            ->where('status', InvoiceStatus::UNPAID)
            ->orderBy('due_date', 'asc')
            ->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Penghuni ' . $tenant->full_name . ' tidak memiliki tagihan yang belum lunas.');
        }

        $result = $reminderService->sendReminder($invoice, ReminderType::MANUAL, force: true);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Pesan pengingat berhasil dikirim ke Telegram ' . $tenant->full_name . '.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim pengingat ke ' . $tenant->full_name . ': ' . $result['message']);
        }
    }
}
