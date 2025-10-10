<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Enum\UserTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = collect([
            [
                'name' => 'أحمد محمد',
                'phone_number' => '0955555555',
                'birth_date' => '1990-01-15',
                'national_id' => '12345678901',
                'gender' => 'male',
                'address' => 'دمشق، سوريا',
                'mother_name' => 'فاطمة أحمد',
            ],
            [
                'name' => 'فاطمة علي',
                'phone_number' => '0966666666',
                'birth_date' => '1995-03-20',
                'national_id' => '09876543210',
                'gender' => 'female',
                'address' => 'حلب، سوريا',
                'mother_name' => 'مريم علي',
            ],
            [
                'name' => 'محمد حسن',
                'phone_number' => '0977777777',
                'birth_date' => '1988-07-10',
                'national_id' => '11223344556',
                'gender' => 'male',
                'address' => 'حمص، سوريا',
                'mother_name' => 'زينب حسن',
            ],
            [
                'name' => 'عائشة أحمد',
                'phone_number' => '0988888888',
                'birth_date' => '1992-12-05',
                'national_id' => '22334455667',
                'gender' => 'female',
                'address' => 'حماة، سوريا',
                'mother_name' => 'خديجة أحمد',
            ],
        ]);

        $customerRole = Role::where('role_name', UserTypeEnum::CUSTOMER->value)->firstOrFail();
        $now = now();

        $usersData = $customers->map(fn($customer): array => [
            'name' => $customer['name'],
            'phone_number' => $customer['phone_number'],
            'password' => Hash::make('Customer@2024'),
            'type' => UserTypeEnum::CUSTOMER->value,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        User::insert($usersData);

        $users = User::whereIn('phone_number', $customers->pluck('phone_number'))->get();

        $userRolesData = $users->map(fn($user): array => [
            'user_id' => $user->id,
            'role_id' => $customerRole->id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert user roles
        DB::table('user_roles')->insert($userRolesData);

        // Prepare customers data for bulk insert
        $customersData = $customers->map(fn(array $customer, $index): array => [
            'user_id' => $users[$index]->id,
            'birth_date' => $customer['birth_date'],
            'national_id' => $customer['national_id'],
            'gender' => $customer['gender'],
            'address' => $customer['address'],
            'mother_name' => $customer['mother_name'],
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert customers
        Customer::insert($customersData);
    }
}
