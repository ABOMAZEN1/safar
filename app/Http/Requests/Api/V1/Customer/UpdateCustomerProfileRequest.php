<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

final class UpdateCustomerProfileRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|ValidationRule>>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            // بيانات المستخدم الأساسية
            'name' => [
                'sometimes',
                'string',
                'max:255',
                'min:2',
            ],
            'phone_number' => [
                'sometimes',
                'string',
                'unique:users,phone_number,' . $userId,
                'regex:/^[0-9+\-\s()]+$/',
                'min:10',
                'max:20',
            ],
            'firebase_token' => [
                'sometimes',
                'string',
                'max:500',
            ],

            // بيانات العميل
            'birth_date' => [
                'sometimes',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'national_id' => [
                'sometimes',
                'string',
                'max:20',
                'min:10',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'sometimes',
                'string',
                'in:male,female',
            ],
            'address' => [
                'sometimes',
                'string',
                'max:500',
                'min:10',
            ],
            'mother_name' => [
                'sometimes',
                'string',
                'max:255',
                'min:2',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // رسائل بيانات المستخدم
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم لا يمكن أن يتجاوز 255 حرف',
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين',
            'phone_number.string' => 'رقم الهاتف يجب أن يكون نصاً',
            'phone_number.unique' => 'رقم الهاتف مستخدم بالفعل',
            'phone_number.regex' => 'رقم الهاتف غير صحيح',
            'phone_number.min' => 'رقم الهاتف يجب أن يكون على الأقل 10 أرقام',
            'phone_number.max' => 'رقم الهاتف لا يمكن أن يتجاوز 20 رقم',
            'firebase_token.string' => 'رمز Firebase يجب أن يكون نصاً',
            'firebase_token.max' => 'رمز Firebase طويل جداً',

            // رسائل بيانات العميل
            'birth_date.date' => 'تاريخ الميلاد يجب أن يكون تاريخ صحيح',
            'birth_date.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم',
            'birth_date.after' => 'تاريخ الميلاد غير صحيح',
            'national_id.string' => 'رقم الهوية يجب أن يكون نصاً',
            'national_id.max' => 'رقم الهوية لا يمكن أن يتجاوز 20 رقم',
            'national_id.min' => 'رقم الهوية يجب أن يكون على الأقل 10 أرقام',
            'national_id.regex' => 'رقم الهوية يجب أن يحتوي على أرقام فقط',
            'gender.string' => 'الجنس يجب أن يكون نصاً',
            'gender.in' => 'الجنس يجب أن يكون ذكر أو أنثى',
            'address.string' => 'العنوان يجب أن يكون نصاً',
            'address.max' => 'العنوان لا يمكن أن يتجاوز 500 حرف',
            'address.min' => 'العنوان يجب أن يكون على الأقل 10 أحرف',
            'mother_name.string' => 'اسم الأم يجب أن يكون نصاً',
            'mother_name.max' => 'اسم الأم لا يمكن أن يتجاوز 255 حرف',
            'mother_name.min' => 'اسم الأم يجب أن يكون على الأقل حرفين',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // تنظيف رقم الهاتف
        if (isset($data['phone_number'])) {
            $data['phone_number'] = preg_replace('/[^0-9+]/', '', $data['phone_number']);
        }

        // تنظيف رقم الهوية
        if (isset($data['national_id'])) {
            $data['national_id'] = preg_replace('/[^0-9]/', '', $data['national_id']);
        }

        $this->merge($data);
    }
}
