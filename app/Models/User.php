<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\Role;
use App\Enums\TenantStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => Role::class,
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'recorded_by');
    }

    public function cleaningReviews()
    {
        return $this->hasMany(CleaningSchedule::class, 'reviewed_by');
    }

    public function handledDamageReports()
    {
        return $this->hasMany(DamageReport::class, 'handled_by');
    }

    public function processedTransfers()
    {
        return $this->hasMany(RoomTransferRequest::class, 'processed_by');
    }

    public function canAccessTenantData(Tenant $tenant): bool
    {
        if ($this->role === Role::PENGGUNI) {
            return $this->tenant && $this->tenant->id === $tenant->id;
        }

        return true;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($this->role->value, $roles);
    }
}
