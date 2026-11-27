<?php

namespace App\Models;

use App\Enums\ReminderStatus;
use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $table = 'reminder_logs';

    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'type',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'type' => ReminderType::class,
        'status' => ReminderStatus::class,
        'sent_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
