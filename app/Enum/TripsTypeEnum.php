<?php

declare(strict_types=1);

namespace App\Enum;

enum TripsTypeEnum: string
{
    case ONE_WAY = 'one_way';

    case TWO_WAY = 'two_way';
}
