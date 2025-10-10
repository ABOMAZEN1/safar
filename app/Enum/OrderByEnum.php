<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderByEnum: string
{
    case PRICE_ASC = 'price_asc';
    case PRICE_DESC = 'price_desc';
    case DEPARTURE_TIME_ASC = 'departure_time_asc';
    case DEPARTURE_TIME_DESC = 'departure_time_desc';
    case AVAILABLE_SEATS_DESC = 'available_seats_desc';
}
