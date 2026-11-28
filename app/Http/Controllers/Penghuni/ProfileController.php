<?php

namespace App\Http\Controllers\Penghuni;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        abort_if($user->role !== Role::PENGGUNI, 403);

        $tenant = $user->tenant()->with('room')->firstOrFail();

        return view('penghuni.profile.edit', compact('user', 'tenant'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        abort_if($user->role !== Role::PENGGUNI, 403);

        $tenant = $user->tenant()->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'origin_address' => ['required', 'string'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'required_with:password_confirmation', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama penghuni wajib diisi.',
            'email.required' => 'Email penghuni wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'origin_address.required' => 'Alamat asal wajib diisi.',
            'guardian_phone.required' => 'No. HP orang tua / wali wajib diisi.',
            'guardian_phone.max' => 'No. HP orang tua / wali maksimal 20 karakter.',
            'password.required_with' => 'Password baru wajib diisi jika konfirmasi password diisi.',
            'password.min' => 'Password baru minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Pastikan kedua kolom password sama.',
        ]);

        DB::transaction(function () use ($user, $tenant, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            $tenant->update([
                'full_name' => $validated['name'],
                'origin_address' => $validated['origin_address'],
                'guardian_phone' => $validated['guardian_phone'],
            ]);
        });

        return redirect()->route('penghuni.profile.edit')
            ->with('success', 'Profil penghuni berhasil diperbarui.');
    }
}
