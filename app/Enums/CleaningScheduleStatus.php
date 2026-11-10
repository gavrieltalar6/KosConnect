<?php

namespace App\Enums;

enum CleaningScheduleStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case RESCHEDULED = 'rescheduled';
    case DONE = 'done';
}
