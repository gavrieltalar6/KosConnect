<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        // Menampilkan daftar tagihan (bisa difilter per tenant/periode nanti)
        $invoices = Invoice::with('tenant')->latest()->paginate(10);
        return view('pemilik.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('pemilik.invoices.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'period' => ['required', 'string'], // Contoh: 2023-10
        ]);

        Invoice::create(array_merge($validated, ['status' => 'unpaid']));

        return redirect()->route('pemilik.invoices.index')->with('success', 'Tagihan berhasil dibuat.');
    }
}
