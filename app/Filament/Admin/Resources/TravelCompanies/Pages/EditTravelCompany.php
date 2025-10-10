<?php

namespace App\Filament\Admin\Resources\TravelCompanies\Pages;

use App\Filament\Admin\Resources\TravelCompanies\TravelCompanyResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditTravelCompany extends EditRecord
{
    protected static string $resource = TravelCompanyResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 🔹 تعديل بيانات المستخدم المرتبط
        if (!empty($data['user'])) {
            $userData = $data['user'];

            // تحقق من اسم المالك
            if (empty($userData['name'])) {
                throw ValidationException::withMessages([
                    'user.name' => 'اسم المالك لا يمكن أن يكون فارغاً.',
                ]);
            }

            // تحقق من رقم الهاتف
            if (empty($userData['phone_number'])) {
                throw ValidationException::withMessages([
                    'user.phone_number' => 'رقم الهاتف لا يمكن أن يكون فارغاً.',
                ]);
            }

            // تحقق من رقم الهاتف المكرر (باستثناء السجل الحالي)
            $exists = $this->record->user->id !== null
                ? \App\Models\User::where('phone_number', $userData['phone_number'])
                    ->where('id', '!=', $this->record->user->id)
                    ->exists()
                : false;
            if ($exists) {
                throw ValidationException::withMessages([
                    'user.phone_number' => 'رقم الهاتف مستخدم بالفعل.',
                ]);
            }

            $user = $this->record->user;
            $user->name = $userData['name'];
            $user->phone_number = $userData['phone_number'];

            // إذا أدخلت كلمة مرور جديدة، نحدثها
            if (!empty($userData['password'])) {
                $user->password = bcrypt($userData['password']);
            }

            $user->save();
        }

        unset($data['user']); // حذف بيانات المستخدم من الحقول قبل حفظ الشركة

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

        return $data;
    }
}
