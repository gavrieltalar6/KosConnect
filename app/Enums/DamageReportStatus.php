<?php

namespace App\Enums;

enum DamageReportStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::OPEN => 'Menunggu Diproses',
            self::IN_PROGRESS => 'Sedang Diproses',
            self::RESOLVED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
