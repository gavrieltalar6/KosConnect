<?php

namespace App\Enums;

enum ReminderStatus: string
{
    case SENT = 'sent';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';
}
