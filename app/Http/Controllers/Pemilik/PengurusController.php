<?php

namespace App\Http\Controllers\Pemilik;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengurusController extends Controller
{
    public function index()
    {
        $pengurus = User::where('role', Role::PENGURUS)->latest()->paginate(10);

        return view('pemilik.pengurus.index', compact('pengurus'));
    }

    public function create()
    {
        return view('pemilik.pengurus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama pengurus wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => Role::PENGURUS,
            'is_active' => true,
        ]);

        return redirect()->route('pemilik.pengurus.index')
            ->with('success', 'Pengurus berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_if($user->role !== Role::PENGURUS, 404);

        return view('pemilik.pengurus.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== Role::PENGURUS, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->update($validated);

        return redirect()->route('pemilik.pengurus.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function deactivate(User $user)
    {
        abort_if($user->role !== Role::PENGURUS, 404);

        $user->update(['is_active' => false]);

        return redirect()->route('pemilik.pengurus.index')
            ->with('success', "Akun pengurus {$user->name} berhasil dinonaktifkan.");
    }

    public function activate(User $user)
    {
        abort_if($user->role !== Role::PENGURUS, 404);

        $user->update(['is_active' => true]);

        return redirect()->route('pemilik.pengurus.index')
            ->with('success', "Akun pengurus {$user->name} berhasil diaktifkan.");
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== Role::PENGURUS, 404);

        $name = $user->name;
        $user->delete();

        return redirect()->route('pemilik.pengurus.index')
            ->with('success', "Akun pengurus {$name} berhasil dihapus.");
    }
}
