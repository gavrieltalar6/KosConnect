<?php

namespace App\Models;

use App\Enums\DamageReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReport extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_id',
        'title',
        'description',
        'status',
        'handled_by',
        'resolved_at',
    ];

    protected $casts = [
        'status' => DamageReportStatus::class,
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
