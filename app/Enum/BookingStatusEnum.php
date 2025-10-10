<?php

declare(strict_types=1);

namespace App\Enum;

enum BookingStatusEnum: string
{
    case CANCELED = 'canceled';

    case UNPAID = 'unpaid';

    case REFUNDED = 'refunded';

    case PAID = 'paid';
}
