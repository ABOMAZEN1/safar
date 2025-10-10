<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Customer;

use Override;
use App\Http\Requests\Api\BaseApiFormRequest;

final class UpdateProfileImageRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'image.required' => 'Profile image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a JPEG, PNG, or JPG.',
            'image.max' => 'The image size must not exceed 2MB.',
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'image' => 'profile image',
        ];
    }
}
