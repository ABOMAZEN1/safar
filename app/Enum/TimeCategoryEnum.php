<?php

declare(strict_types=1);

namespace App\Enum;

enum TimeCategoryEnum: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case EVENING = 'evening';
    case NIGHT = 'night';

    public function getDescription(): string
    {
        return match ($this) {
            self::MORNING => '06:00 - 11:59',
            self::AFTERNOON => '12:00 - 17:59',
            self::EVENING => '18:00 - 23:59',
            self::NIGHT => '00:00 - 05:59',
        };
    }
}
