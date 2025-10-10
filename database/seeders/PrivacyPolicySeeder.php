<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use Illuminate\Database\Seeder;

final class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentTimestamp = now();

        $privacyPolicies = collect([
            [
                'title' => 'How do we protect your personal information?',
                'description' => 'We implement industry-standard security measures to protect your data. This includes encryption of sensitive information, secure servers, and regular security audits. Your personal information is only accessible to authorized personnel.',
            ],
            [
                'title' => 'What personal information do we collect?',
                'description' => 'We collect basic information required for booking and identification, including name, phone number, email address, and travel preferences. Payment information is processed securely through our payment gateway partners.',
            ],
            [
                'title' => 'How do we use your data?',
                'description' => 'Your data is used primarily for processing bookings, sending travel updates, and improving our services. We never sell your personal information to third parties. We may use anonymized data for analytical purposes.',
            ],
        ])->map(fn($policy): array => array_merge($policy, [
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]));

        PrivacyPolicy::insert($privacyPolicies->toArray());
    }
}
