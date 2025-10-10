<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Enum\UserTypeEnum;
use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use App\Models\Role;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTravelCompany extends CreateRecord
{
    protected static string $resource = TravelCompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $owner = $data['owner'] ?? [];

            // Check if phone number already exists in users table
            if (!empty($owner['phone_number'])) {
                $exists = User::where('phone_number', $owner['phone_number'])->exists();
                if ($exists) {
                    throw ValidationException::withMessages([
                        'owner.phone_number' => 'رقم الهاتف مستخدم بالفعل.',
                    ]);
                }
            }

            $user = User::create([
                'name'           => $owner['name'] ?? '',
                'phone_number'   => $owner['phone_number'] ?? '',
                'password'       => $owner['password'] ?? '',
                'type'           => 'travel_company',
                'verified_at'    => now(),
                'profile_image_path' => null,
            ]);

            $data['user_id'] = $user->id;
            unset($data['owner']);

            return $data;
        });
    }
}