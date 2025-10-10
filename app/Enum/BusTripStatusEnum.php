<?php

declare(strict_types=1);

namespace App\Enum;

enum BusTripStatusEnum: string
{
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case ACTIVE = 'active';

    /**
     * Get the Arabic translation for the status.
     */
    public function getArabicLabel(): string
    {
        return match ($this) {
            self::COMPLETED => 'منتهية',
            self::CANCELED => 'ملغاة',
            self::ACTIVE => 'نشطة',
        };
    }
}
