<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\Role;
use App\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
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
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'origin_address' => ['required', 'string'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ];

        if ($request->filled('password_confirmation')) {
            $rules['password'][] = 'confirmed';
        }

        $validated = $request->validate($rules, [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'email.unique' => 'Email ini sudah terdaftar sebagai pengguna.',
        ]);

        if ($this->roomIsTaken($validated['room_id'] ?? null)) {
            return back()->withInput()->with('error', 'Kamar tersebut sudah dihuni penghuni lain.');
        }

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => Role::PENGGUNI,
                'is_active' => true,
            ]);

            $tenant = Tenant::create([
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

            $tenant->room?->syncOccupancyStatus();
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

        if ($this->roomIsTaken($validated['room_id'] ?? null, $tenant->id)) {
            return back()->withInput()->with('error', 'Kamar tersebut sudah dihuni penghuni lain.');
        }

        DB::transaction(function () use ($validated, $tenant) {
            $oldRoomId = $tenant->room_id;

            $tenant->update($validated);
            $tenant->user->update(['name' => $validated['full_name']]);

            // Sinkronkan kamar lama (boleh jadi kosong) dan kamar baru
            Room::find($oldRoomId)?->syncOccupancyStatus();
            Room::find($validated['room_id'] ?? null)?->syncOccupancyStatus();
        });

        return redirect()->route('pemilik.tenants.index')->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function deactivate(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            $tenant->update(['status' => TenantStatus::INACTIVE]);
            $tenant->user?->update(['is_active' => false]);
            $tenant->room?->syncOccupancyStatus();
        });

        return redirect()->route('pemilik.tenants.index')
            ->with('success', "Akun penghuni {$tenant->full_name} berhasil dinonaktifkan.");
    }

    public function activate(Tenant $tenant)
    {
        if ($this->roomIsTaken($tenant->room_id, $tenant->id)) {
            return back()->with('error', 'Kamar penghuni ini sudah ditempati penghuni aktif lain. Silakan ubah kamar melalui Edit Detail terlebih dahulu.');
        }

        DB::transaction(function () use ($tenant) {
            $tenant->update(['status' => TenantStatus::ACTIVE]);
            $tenant->user?->update(['is_active' => true]);
            $tenant->room?->syncOccupancyStatus();
        });

        return redirect()->route('pemilik.tenants.index')
            ->with('success', "Akun penghuni {$tenant->full_name} berhasil diaktifkan.");
    }

    public function destroy(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            $roomId = $tenant->room_id;
            $user = $tenant->user;

            $tenant->delete();
            $user?->delete();

            Room::find($roomId)?->syncOccupancyStatus();
        });

        return redirect()->route('pemilik.tenants.index')->with('success', 'Penghuni berhasil dihapus.');
    }

    /**
     * Kamar dianggap terisi jika sudah ada penghuni aktif lain di sana.
     */
    private function roomIsTaken(?int $roomId, ?int $exceptTenantId = null): bool
    {
        if (! $roomId) {
            return false;
        }

        $room = Room::find($roomId);

        if (! $room) {
            return false;
        }

        $query = $room->activeTenants();

        if ($exceptTenantId) {
            $query = $query->where('tenants.id', '!=', $exceptTenantId);
        }

        return $query->exists();
    }
}