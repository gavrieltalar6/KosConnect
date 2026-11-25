<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\RoomStatus;
use App\Enums\TenantStatus;
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

    /**
     * Penghuni yang benar-benar menghuni kamar ini:
     * tenant berstatus aktif DAN akun user-nya juga aktif.
     */
    public function activeTenants()
    {
        return $this->hasMany(Tenant::class)
            ->where('tenants.status', TenantStatus::ACTIVE->value)
            ->whereHas('user', fn ($query) => $query->where('is_active', true));
    }

    public function hasActiveTenant(): bool
    {
        return $this->activeTenants()->exists();
    }

    /**
     * Samakan status kamar dengan data penghuni.
     * - Ada penghuni aktif  -> occupied
     * - Tidak ada penghuni tapi masih tertulis occupied -> available
     * - Selain itu (available / maintenance) dibiarkan.
     */
    public function syncOccupancyStatus(): void
    {
        $hasActiveTenant = $this->hasActiveTenant();

        if ($hasActiveTenant && $this->status !== RoomStatus::OCCUPIED) {
            $this->update(['status' => RoomStatus::OCCUPIED]);
        } elseif (! $hasActiveTenant && $this->status === RoomStatus::OCCUPIED) {
            $this->update(['status' => RoomStatus::AVAILABLE]);
        }
    }

    /**
     * Terapkan harga sewa terbaru ke tagihan yang BELUM lunas di kamar ini.
     * Tagihan yang sudah lunas tidak diubah (riwayat pembayaran tetap).
     */
    public function syncUnpaidInvoiceAmounts(): void
    {
        Invoice::where('room_id', $this->id)
            ->where('status', InvoiceStatus::UNPAID->value)
            ->update(['amount' => $this->monthly_price, 'updated_at' => now()]);
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