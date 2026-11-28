<?php

namespace App\Http\Controllers\Pengurus;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        abort_if($user->role !== Role::PENGURUS, 403);

        return view('pengurus.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_if($user->role !== Role::PENGURUS, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'required_with:password_confirmation', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama pengurus wajib diisi.',
            'email.required' => 'Email pengurus wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.required_with' => 'Password baru wajib diisi jika konfirmasi password diisi.',
            'password.min' => 'Password baru minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Pastikan kedua kolom password sama.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('pengurus.profile.edit')
            ->with('success', 'Profil pengurus berhasil diperbarui.');
    }
}
