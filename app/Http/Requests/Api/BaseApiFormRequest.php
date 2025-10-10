<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Override;
use App\Helpers\ApiResponse;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiFormRequest extends BaseFormRequest
{
    #[Override]
    protected function failedValidation(Validator $validator): void
    {
        $jsonResponse = ApiResponse::error(
            message: __('messages.errors.validation.validation_failed'),
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            data: $validator->errors(),
        );

        throw new HttpResponseException($jsonResponse);
    }
}
