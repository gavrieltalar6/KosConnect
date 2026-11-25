<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->value, ['pemilik', 'pengurus']);
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return in_array($user->role->value, ['pemilik', 'pengurus']) || $user->id === $tenant->user_id;
    }

    public function create(User $user): bool
    {
        return $user->role->value === 'pemilik';
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->role->value === 'pemilik';
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->role->value === 'pemilik';
    }

    /**
     * Determine whether the user can view the sensitive data of the tenant.
     */
    public function viewSensitiveData(User $user, Tenant $tenant): bool
    {
        // Pemilik dan Pengurus punya akses
        if (in_array($user->role->value, ['pemilik', 'pengurus'])) {
            return true;
        }

        // Penghuni hanya bisa melihat data pribadi mereka sendiri
        return $user->id === $tenant->user_id;
    }
}
