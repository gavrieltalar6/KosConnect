<?php

namespace App\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';

    public function getLabel(): string
    {
        return match($this) {
            self::AVAILABLE => 'Tersedia',
            self::OCCUPIED => 'Terisi',
            self::MAINTENANCE => 'Perawatan',
        };
    }
}
