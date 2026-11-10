<?php

namespace App\Enums;

enum DamageReportStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CANCELLED = 'cancelled';
}
