<?php

namespace App\Models;

use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'room_id',
        'full_name',
        'phone',
        'origin_address',
        'guardian_phone',
        'telegram_chat_id',
        'telegram_link_token',
        'check_in_date',
        'due_day',
        'status',
    ];

    protected $casts = [
        'status' => TenantStatus::class,
        'check_in_date' => 'date',
        'due_day' => 'integer',
        'telegram_chat_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function cleaningSchedules()
    {
        return $this->hasMany(CleaningSchedule::class);
    }

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class);
    }

    public function transferRequests()
    {
        return $this->hasMany(RoomTransferRequest::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }
}
