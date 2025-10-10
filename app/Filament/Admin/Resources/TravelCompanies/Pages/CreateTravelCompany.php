<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTravelCompany extends CreateRecord
{
    protected static string $resource = TravelCompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // استخدم نفس البنية كما في التعديل، البيانات تأتي من 'user' وليس 'owner'
        $userData = $data['user'] ?? [];

        // 🔹 التحقق من وجود اسم المالك
        if (empty($userData['name'])) {
            throw ValidationException::withMessages([
                'user.name' => 'يرجى إدخال اسم المالك.',
            ]);
        }

        // 🔹 التحقق من رقم الهاتف
        if (empty($userData['phone_number'])) {
            throw ValidationException::withMessages([
                'user.phone_number' => 'يرجى إدخال رقم جوال المالك.',
            ]);
        }

        // 🔹 التحقق من رقم الهاتف إذا كان مكرر
        $exists = User::where('phone_number', $userData['phone_number'])->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'user.phone_number' => 'رقم الهاتف مستخدم بالفعل.',
            ]);
        }

        // التحقق من كلمة المرور
        if (empty($userData['password'])) {
            throw ValidationException::withMessages([
                'user.password' => 'يرجى إدخال كلمة المرور.',
            ]);
        }

        // 🔹 تحقق من صورة الشركة
        if (!isset($data['image_path']) || empty($data['image_path'])) {
            throw ValidationException::withMessages([
                'image_path' => 'يجب رفع شعار الشركة.',
            ]);
        }

        // 🔹 تحقق من نسبة الربح
        if (!isset($data['commission_amount']) || $data['commission_amount'] === null) {
            throw ValidationException::withMessages([
                'commission_amount' => 'يرجى إدخال نسبة الربح.',
            ]);
        }

        // إنشاء المستخدم وربطه في transaction
        return DB::transaction(function () use ($data, $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'phone_number' => $userData['phone_number'],
                'password' => bcrypt($userData['password']),
                'type' => 'travel_company',
                'verified_at' => now(),
            ]);

            // ربط الشركة بالمستخدم الجديد
            $data['user_id'] = $user->id;

            // نحذف بيانات user حتى لا تخزن في travel_companies
            unset($data['user']);

            return $data;
        });
    }
}
