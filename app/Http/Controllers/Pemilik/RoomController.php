<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::latest()->paginate(10);
        return view('pemilik.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $statuses = RoomStatus::cases();
        return view('pemilik.rooms.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number'],
            'floor' => ['nullable', 'integer', 'min:1'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', new Enum(RoomStatus::class)],
            'description' => ['nullable', 'string'],
        ], [
            'room_number.required' => 'Nomor kamar wajib diisi.',
            'room_number.unique' => 'Nomor kamar sudah terdaftar.',
            'monthly_price.required' => 'Harga bulanan wajib diisi.',
            'monthly_price.numeric' => 'Harga harus berupa angka.',
            'status.required' => 'Status kamar wajib dipilih.',
        ]);

        Room::create($validated);

        return redirect()->route('pemilik.rooms.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        $statuses = RoomStatus::cases();
        return view('pemilik.rooms.edit', compact('room', 'statuses'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number,' . $room->id],
            'floor' => ['nullable', 'integer', 'min:1'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', new Enum(RoomStatus::class)],
            'description' => ['nullable', 'string'],
        ], [
            'room_number.required' => 'Nomor kamar wajib diisi.',
            'room_number.unique' => 'Nomor kamar sudah terdaftar.',
            'monthly_price.required' => 'Harga bulanan wajib diisi.',
            'monthly_price.numeric' => 'Harga harus berupa angka.',
            'status.required' => 'Status kamar wajib dipilih.',
        ]);

        $room->update($validated);

        return redirect()->route('pemilik.rooms.index')->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->tenants()->count() > 0) {
            return back()->with('error', 'Kamar tidak dapat dihapus karena masih terikat dengan data penghuni.');
        }

        $room->delete();

        return redirect()->route('pemilik.rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }
}
