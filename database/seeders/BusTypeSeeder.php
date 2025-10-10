<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class BusTypeSeeder extends Seeder
{
    public function run(): void
    {
        $busTypes = [
            [
                'name' => 'Standard Bus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VIP Bus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mini Bus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bus_types')->insert($busTypes);
    }
}
