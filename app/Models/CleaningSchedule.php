<?php

namespace App\Models;

use App\Enums\CleaningScheduleStatus;
use App\Enums\PresenceStatus;
use Illuminate\Database\Eloquent\Model;

class CleaningSchedule extends Model
{
    protected $table = 'cleaning_schedules';

    protected $fillable = [
        'tenant_id',
        'room_id',
        'requested_date',
        'requested_time',
        'presence_status',
        'status',
        'reviewed_by',
        'rescheduled_date',
        'rescheduled_time',
        'note',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'status' => CleaningScheduleStatus::class,
        'presence_status' => PresenceStatus::class,
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
