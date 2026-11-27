<?php

namespace App\Enums;

enum ReminderType: string
{
    case H_MINUS_7 = 'h_minus_7';
    case H_MINUS_3 = 'h_minus_3';
    case H_MINUS_1 = 'h_minus_1';
    case H_DAY = 'h_day';
    case MANUAL = 'manual';

    public function getLabel(): string
    {
        return match ($this) {
            self::H_MINUS_7 => 'H-7',
            self::H_MINUS_3 => 'H-3',
            self::H_MINUS_1 => 'H-1',
            self::H_DAY => 'Hari H',
            self::MANUAL => 'Manual',
        };
    }
}
