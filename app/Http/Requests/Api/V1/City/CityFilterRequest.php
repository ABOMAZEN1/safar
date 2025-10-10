<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\City;

use App\DataTransferObjects\City\CityFilterDTO;
use App\Http\Requests\Api\BaseApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Override;

final class CityFilterRequest extends BaseApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        $supportedLanguages = Config::get('languages.supported', ['en']);

        return [
            'language' => [
                'nullable',
                'string',
                'in:' . implode(',', $supportedLanguages),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'language.in' => 'The specified language is not supported.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'language' => 'language',
        ];
    }

    /**
     * Convert the validated data to a DTO.
     */
    public function toDTO(): CityFilterDTO
    {
        return new CityFilterDTO(
            language: $this->validated('language'),
        );
    }
}
