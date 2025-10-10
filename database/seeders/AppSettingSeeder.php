<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

final class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            [
                'key' => 'app_name',
                'string_value' => 'Bus Ticket System',
                'numeric_value' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_commission',
                'string_value' => null,
                'numeric_value' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'booking_cancellation_hours',
                'string_value' => null,
                'numeric_value' => 24,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        AppSetting::insert($settings);
    }
}
