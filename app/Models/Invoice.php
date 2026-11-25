<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'period',
        'amount',
        'due_date',
        'status',
        'paid_at',
        'midtrans_order_id',
        'snap_token',
        'snap_token_created_at',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'snap_token_created_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reminderLogs()
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::UNPAID && $this->due_date->isPast();
    }

    public function canBeAccessedBy(User $user): bool
    {
        if ($user->role === Role::PENGGUNI) {
            return $this->tenant->user_id === $user->id;
        }

        return true;
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}