<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TravelCompany;
use App\Models\TravelCompanyCommission;
use Illuminate\Database\Seeder;

final class TravelCompanyCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $companies = TravelCompany::all();

        $commissions = $companies->map(fn ($company): array => [
            'travel_company_id' => $company->id,
            'commission_amount' => random_int(5, 15), // Random commission between 5% and 15%
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        TravelCompanyCommission::insert($commissions);
    }
}
