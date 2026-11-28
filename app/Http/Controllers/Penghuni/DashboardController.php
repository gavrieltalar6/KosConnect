<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Cari data penghuni berdasarkan akun yang sedang login
        $tenant = Tenant::where('user_id', Auth::id())->first();

        // Ambil hanya tagihan yang belum dibayar
        $unpaidInvoices = Invoice::where('tenant_id', $tenant?->id)
            ->where('status', InvoiceStatus::UNPAID->value)
            ->orderBy('due_date')
            ->get();

        return view('penghuni.dashboard', compact(
            'tenant',
            'unpaidInvoices'
        ));
    }
}
