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

    /**
     * Mendapatkan log reminder berdasarkan tipe pengingat (H-7, H-1, Hari H).
     */
    public function getReminderLog(string $type): ?ReminderLog
    {
        if ($this->relationLoaded('reminderLogs')) {
            return $this->reminderLogs->firstWhere('type.value', $type) 
                ?? $this->reminderLogs->firstWhere('type', $type);
        }

        return $this->reminderLogs()->where('type', $type)->latest()->first();
    }

    /**
     * Mendapatkan status badge untuk box pengingat ('sent' = biru, 'failed' = merah, 'pending' = abu).
     */
    public function getReminderBoxState(string $type): string
    {
        $log = $this->getReminderLog($type);
        if (!$log) {
            return 'pending'; // Belum terkirim (Abu)
        }

        $val = is_string($log->status) ? $log->status : $log->status?->value;
        if ($val === 'sent') {
            return 'sent'; // Terkirim (Biru)
        } elseif ($val === 'failed') {
            return 'failed'; // Gagal (Merah)
        }

        return 'pending'; // Default: Abu
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