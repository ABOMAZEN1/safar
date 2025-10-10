<?php

declare(strict_types=1);

namespace App\Enum;

enum UserBookingsStatus: string
{
    case ALL = 'all';

    case UPCOMING = 'upcoming';

    case PASSED = 'passed';
}
