<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFcmTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firebase_token' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9-_]+$/'
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
            'firebase_token.required' => 'FCM token is required.',
            'firebase_token.string' => 'FCM token must be a string.',
            'firebase_token.max' => 'FCM token may not be greater than 255 characters.',
            'firebase_token.regex' => 'FCM token format is invalid.',
        ];
    }
}
