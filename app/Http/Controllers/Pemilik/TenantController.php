<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Room;
use App\Enums\Role;
use App\Enums\TenantStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('user', 'room')->latest()->paginate(10);
        return view('pemilik.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('pemilik.tenants.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'origin_address' => ['required', 'string'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => Role::PENGHUNI,
                'is_active' => true,
            ]);

            Tenant::create([
                'user_id' => $user->id,
                'room_id' => $validated['room_id'] ?? null,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'origin_address' => $validated['origin_address'],
                'guardian_phone' => $validated['guardian_phone'],
                'check_in_date' => $validated['check_in_date'],
                'due_day' => $validated['due_day'],
                'status' => TenantStatus::ACTIVE,
            ]);

            if ($validated['room_id']) {
                Room::find($validated['room_id'])->update(['status' => 'occupied']);
            }
        });

        return redirect()->route('pemilik.tenants.index')->with('success', 'Penghuni berhasil ditambahkan.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('user', 'room');
        return view('pemilik.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $rooms = Room::where('status', 'available')->orWhere('id', $tenant->room_id)->get();
        return view('pemilik.tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'origin_address' => ['required', 'string'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date'],
            'due_day' => ['required', 'integer', 'between:1,28'],
            'status' => ['required', Rule::enum(TenantStatus::class)],
        ]);

        DB::transaction(function () use ($validated, $tenant) {
            $oldRoomId = $tenant->room_id;

            $tenant->update($validated);
            $tenant->user->update(['name' => $validated['full_name']]);

            if ($oldRoomId !== $validated['room_id']) {
                if ($oldRoomId) Room::find($oldRoomId)->update(['status' => 'available']);
                if ($validated['room_id']) Room::find($validated['room_id'])->update(['status' => 'occupied']);
            }
        });

        return redirect()->route('pemilik.tenants.index')->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            if ($tenant->room_id) {
                Room::find($tenant->room_id)->update(['status' => 'available']);
            }
            $tenant->user->delete();
            $tenant->delete();
        });

        return redirect()->route('pemilik.tenants.index')->with('success', 'Penghuni berhasil dihapus.');
    }
}
