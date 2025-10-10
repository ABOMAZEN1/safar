<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name_en' => 'Damascus',
                'name_ar' => 'دمشق',
                'population' => 2685360,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Aleppo',
                'name_ar' => 'حلب',
                'population' => 2132100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Homs',
                'name_ar' => 'حمص',
                'population' => 652609,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Latakia',
                'name_ar' => 'اللاذقية',
                'population' => 383786,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Hama',
                'name_ar' => 'حماة',
                'population' => 312994,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Raqqa',
                'name_ar' => 'الرقة',
                'population' => 220488,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Deir ez-Zor',
                'name_ar' => 'دير الزور',
                'population' => 211857,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Al-Hasakah',
                'name_ar' => 'الحسكة',
                'population' => 188160,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Tartus',
                'name_ar' => 'طرطوس',
                'population' => 115769,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Idlib',
                'name_ar' => 'ادلب',
                'population' => 98791,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Daraa',
                'name_ar' => 'درعا',
                'population' => 97969,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'As-Suwayda',
                'name_ar' => 'السويداء',
                'population' => 73641,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Quneitra',
                'name_ar' => 'القنيطرة',
                'population' => 153,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Rural Damascus',
                'name_ar' => 'ريف دمشق',
                'population' => 2836000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cities')->insert($cities);
    }
}
