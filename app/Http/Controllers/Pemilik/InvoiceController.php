<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('tenant', 'room')->latest()->paginate(10);
        return view('pemilik.invoices.index', compact('invoices'));
    }

    public function create()
    {
        // Ambil kamar sekalian supaya harga sewa bisa ditampilkan di dropdown
        $tenants = Tenant::with('room')->where('status', 'active')->get();
        return view('pemilik.invoices.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'period' => [
                'required',
                'string',
                Rule::unique('invoices', 'period')
                    ->where(fn ($query) => $query->where('tenant_id', $request->input('tenant_id'))),
            ],
            'due_date' => ['required', 'date'],
        ], [
            'period.unique' => 'Tagihan untuk penghuni ini di periode tersebut sudah ada.',
        ]);

        $tenant = Tenant::with('room')->findOrFail($validated['tenant_id']);

        if (! $tenant->room) {
            return back()
                ->withInput()
                ->with('error', 'Penghuni ini belum memiliki kamar. Tetapkan kamar terlebih dahulu.');
        }

        // Jumlah tagihan selalu mengikuti harga sewa kamar yang sedang dihuni
        Invoice::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'period' => $validated['period'],
            'amount' => $tenant->room->monthly_price,
            'due_date' => $validated['due_date'],
            'status' => InvoiceStatus::UNPAID,
        ]);

        return redirect()->route('pemilik.invoices.index')->with('success', 'Tagihan berhasil dibuat.');
    }
}