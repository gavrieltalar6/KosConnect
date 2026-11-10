<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $primaryKey = 'id';

    protected $fillable = [
        'room_number',
        'floor',
        'monthly_price',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => RoomStatus::class,
        'monthly_price' => 'decimal:2',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
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
        return $this->hasMany(RoomTransferRequest::class, 'from_room_id');
    }

    public function targetRoomTransferRequests()
    {
        return $this->hasMany(RoomTransferRequest::class, 'to_room_id');
    }
}