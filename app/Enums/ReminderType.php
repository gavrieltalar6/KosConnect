<?php

namespace App\Enums;

enum ReminderType: string
{
    case H_MINUS_3 = 'h_minus_3';
    case H_MINUS_1 = 'h_minus_1';
    case MANUAL = 'manual';
}
