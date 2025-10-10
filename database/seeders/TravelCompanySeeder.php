<?php

declare(strict_types=1);

namespace Database\Seeders;

use Exception;
use App\Enum\UserTypeEnum;
use App\Models\Role;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/**
 * Travel Company Seeder
 * 
 * This seeder creates travel companies with their associated users for API testing.
 * Each company has:
 * - Phone number for login (API endpoint: /api/v1/auth/travel-companies/login)
 * - Password: TravelCompany@2024
 * - Company details (name, address, contact info)
 * 
 * API Login Example:
 * POST /api/v1/auth/travel-companies/login
 * {
 *   "phone_number": "0934567890",
 *   "password": "TravelCompany@2024"
 * }
 */
final class TravelCompanySeeder extends Seeder
{
    public function run(): void
    {
        $travelCompanies = collect([
            [
                'name' => 'شركة النقل السوري',
                'phone_number' => '0934567890',
                'company_name' => 'Syrian Transport Company',
                'contact_number' => '0934567891',
                'address' => 'دمشق، سوريا',
                'image_path' => 'uploads/travel_company_images/syrian_transport_logo.jpeg',
            ],
            [
                'name' => 'خطوط حلب للنقل',
                'phone_number' => '0945678901',
                'company_name' => 'Aleppo Bus Lines',
                'contact_number' => '0945678902',
                'address' => 'حلب، سوريا',
                'image_path' => 'uploads/travel_company_images/aleppo_bus_logo.jpeg',
            ],
            [
                'name' => 'شركة السفر الذهبي',
                'phone_number' => '0956789012',
                'company_name' => 'Golden Travel Company',
                'contact_number' => '0956789013',
                'address' => 'حمص، سوريا',
                'image_path' => 'uploads/travel_company_images/golden_travel_logo.jpeg',
            ],
            [
                'name' => 'النقل السريع',
                'phone_number' => '0967890123',
                'company_name' => 'Fast Transport',
                'contact_number' => '0967890124',
                'address' => 'حماة، سوريا',
                'image_path' => 'uploads/travel_company_images/fast_transport_logo.jpeg',
            ],
            [
                'name' => 'شركة الرحلات المتميزة',
                'phone_number' => '0978901234',
                'company_name' => 'Premium Travel Company',
                'contact_number' => '0978901235',
                'address' => 'اللاذقية، سوريا',
                'image_path' => 'uploads/travel_company_images/premium_travel_logo.jpeg',
            ],
            [
                'name' => 'خطوط الشمال للنقل',
                'phone_number' => '0989012345',
                'company_name' => 'North Transport Lines',
                'contact_number' => '0989012346',
                'address' => 'إدلب، سوريا',
                'image_path' => 'uploads/travel_company_images/north_transport_logo.jpeg',
            ],
            [
                'name' => 'شركة السفر الآمن',
                'phone_number' => '0990123456',
                'company_name' => 'Safe Travel Company',
                'contact_number' => '0990123457',
                'address' => 'درعا، سوريا',
                'image_path' => 'uploads/travel_company_images/safe_travel_logo.jpeg',
            ],
            [
                'name' => 'النقل المتطور',
                'phone_number' => '0901234567',
                'company_name' => 'Advanced Transport',
                'contact_number' => '0901234568',
                'address' => 'القنيطرة، سوريا',
                'image_path' => 'uploads/travel_company_images/advanced_transport_logo.jpeg',
            ],
        ]);

        $travelCompanyRole = Role::where('role_name', UserTypeEnum::TRAVEL_COMPANY->value)->firstOrFail();

        if (! $travelCompanyRole) {
            throw new RuntimeException('Travel company role not found. Please run RoleSeeder first.');
        }


        try {
            DB::beginTransaction();
            $now = now();
            
            // Check if travel companies already exist
            $existingPhoneNumbers = User::where('type', UserTypeEnum::TRAVEL_COMPANY->value)
                ->whereIn('phone_number', $travelCompanies->pluck('phone_number'))
                ->pluck('phone_number')
                ->toArray();
                
            if (!empty($existingPhoneNumbers)) {
                $this->command->warn('Some travel companies already exist with phone numbers: ' . implode(', ', $existingPhoneNumbers));
                $this->command->info('Skipping existing companies and creating new ones...');
                
                // Filter out existing phone numbers
                $travelCompanies = $travelCompanies->filter(function($company) use ($existingPhoneNumbers) {
                    return !in_array($company['phone_number'], $existingPhoneNumbers);
                });
                
                if ($travelCompanies->isEmpty()) {
                    $this->command->info('All travel companies already exist. No new companies to create.');
                    DB::commit();
                    return;
                }
            }

            $usersData = $travelCompanies->map(fn($travelCompany): array => [
                'name' => $travelCompany['name'],
                'phone_number' => $travelCompany['phone_number'],
                'password' => Hash::make('TravelCompany@2024'),
                'type' => UserTypeEnum::TRAVEL_COMPANY->value,
                'verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            // Bulk insert users
            User::insert($usersData);

            // Get the inserted users
            $users = User::whereIn('phone_number', $travelCompanies->pluck('phone_number'))->get();

            if ($users->isEmpty()) {
                throw new RuntimeException('Failed to create users.');
            }

            // Prepare user roles data for bulk insert
            $userRolesData = $users->map(fn($user): array => [
                'user_id' => $user->id,
                'role_id' => $travelCompanyRole->id,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            // Bulk insert user roles
            DB::table('user_roles')->insert($userRolesData);

            // Prepare companies data for bulk insert
            $travelCompaniesData = $travelCompanies->map(fn(array $travelCompany, $index): array => [
                'user_id' => $users[$index]->id,
                'company_name' => $travelCompany['company_name'],
                'contact_number' => $travelCompany['contact_number'],
                'address' => $travelCompany['address'],
                'image_path' => $travelCompany['image_path'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            // Bulk insert companies
            TravelCompany::insert($travelCompaniesData);

            DB::commit();
            
            $this->command->info('Successfully created ' . count($travelCompanies) . ' travel companies.');
            $this->command->info('API Login credentials:');
            $this->command->info('Phone numbers: ' . $travelCompanies->pluck('phone_number')->implode(', '));
            $this->command->info('Password: TravelCompany@2024');
            $this->command->info('API Endpoint: POST /api/v1/auth/travel-companies/login');
            
        } catch (Exception $exception) {
            DB::rollBack();

            throw new RuntimeException('Failed to seed travel companies: ' . $exception->getMessage(), 0, $exception);
        }
    }
}
