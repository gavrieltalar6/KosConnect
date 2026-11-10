<?php

namespace App\Enums;

enum Role: string
{
    case PEMILIK = 'pemilik';
    case PENGURUS = 'pengurus';
    case PENGGUNI = 'penghuni';
}