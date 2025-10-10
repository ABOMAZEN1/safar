<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TermsAndConditions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class TermsAndConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $termsAndConditions = collect([
            [
                'title' => 'What are the booking cancellation policies?',
                'description' => 'Cancellations made 24 hours before departure are eligible for a full refund. Cancellations within 24 hours of departure will incur a 50% cancellation fee. No-shows will not receive a refund.',
            ],
            [
                'title' => 'How early should I arrive before my bus departure?',
                'description' => 'Passengers should arrive at least 15 minutes before the scheduled departure time. Boarding closes 5 minutes before departure. Late arrivals may result in seat forfeiture without refund.',
            ],
            [
                'title' => 'What is the luggage policy?',
                'description' => 'Each passenger is allowed one piece of carry-on luggage (max 7kg) and one piece of stored luggage (max 20kg). Additional luggage may incur extra charges. Dangerous goods and illegal items are strictly prohibited.',
            ],
        ])->map(fn($terms): array => array_merge($terms, [
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        TermsAndConditions::insert($termsAndConditions->toArray());
    }
}
