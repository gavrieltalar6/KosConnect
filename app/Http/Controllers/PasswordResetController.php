<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna yang bisa direset password-nya oleh Pemilik & Pengurus.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        // Hanya tampilkan role pengurus dan penghuni (hindari reset password sesama pemilik)
        $query = User::with('tenant.room')
            ->whereIn('role', [Role::PENGURUS->value, Role::PENGGUNI->value]);

        // Filter pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $roleFilter = $request->input('role');
            if (in_array($roleFilter, [Role::PENGURUS->value, Role::PENGGUNI->value])) {
                $query->where('role', $roleFilter);
            }
        }

        // Sorting / Pengurutan data
        $sort = $request->input('sort', 'default');
        if ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'role_pengurus') {
            $query->orderBy('role', 'asc')->orderBy('name', 'asc');
        } elseif ($sort === 'role_penghuni') {
            $query->orderBy('role', 'desc')->orderBy('name', 'asc');
        } else {
            $query->orderBy('role', 'asc')->orderBy('name', 'asc');
        }

        $users = $query->paginate(10)->withQueryString();

        return view('password_resets.index', compact('users'));
    }

    /**
     * Tampilkan form untuk mereset password akun tertentu.
     */
    public function edit(User $user)
    {
        // Proteksi: tidak mengizinkan reset password pemilik
        if ($user->role === Role::PEMILIK) {
            abort(403, 'Tidak diizinkan mengubah password akun pemilik.');
        }

        return view('password_resets.edit', compact('user'));
    }

    /**
     * Simpan password baru setelah dimasukkan 2 kali (konfirmasi).
     */
    public function update(Request $request, User $user)
    {
        // Proteksi: tidak mengizinkan reset password pemilik
        if ($user->role === Role::PEMILIK) {
            abort(403, 'Tidak diizinkan mengubah password akun pemilik.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Harap masukkan password yang sama pada kedua kolom.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $roleLabel = $user->role === Role::PENGURUS ? 'Pengurus' : 'Penghuni';

        return redirect()->route('password_resets.index')
            ->with('success', "Password untuk akun {$roleLabel} ({$user->name} - {$user->email}) berhasil diperbarui.");
    }
}
