<?php

namespace App\Enums;

enum ReminderStatus: string
{
    case SENT = 'sent';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';

    public function getLabel(): string
    {
        return match ($this) {
            self::SENT => 'Terkirim',
            self::FAILED => 'Gagal',
            self::SKIPPED => 'Dilewati',
        };
    }
}
