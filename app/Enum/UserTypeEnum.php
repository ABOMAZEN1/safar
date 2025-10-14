<?php

declare(strict_types=1);

namespace App\Enum;

enum UserTypeEnum: string
{
    case SUPER_ADMIN = 'super_admin';

    case CUSTOMER = 'customer';

    case BUS_DRIVER = 'bus_driver';

    case ASSISTANT_DRIVER = 'assistant_driver';

    case TRAVEL_COMPANY = 'travel_company';
}
