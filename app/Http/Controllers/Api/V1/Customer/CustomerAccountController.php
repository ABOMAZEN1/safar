<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Customer;

use Exception;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Auth\Customer\CustomerAuthService;
use App\Http\Requests\Api\V1\Customer\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\V1\Customer\UpdateProfileImageRequest;
use App\Services\Customer\CustomerService;
use App\Http\Requests\Api\V1\User\ResetPasswordRequest;
use App\Services\User\UserService;

final class CustomerAccountController extends BaseApiController
{
    public function __construct(
        private readonly CustomerAuthService $customerAuthService,
        private readonly CustomerService $customerService,
        private readonly UserService $userService,
    ) {}

    public function updatePassword(UpdatePasswordRequest $updatePasswordRequest): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (! $user) {
                return $this->errorResponse(
                    message: 'messages.errors.generic.unauthorized',
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            $this->customerAuthService->updatePassword([
                'phone_number' => $user->phone_number,
                'current_password' => $updatePasswordRequest->validated('current_password'),
                'password' => $updatePasswordRequest->validated('password'),
            ]);

            return $this->successResponse(
                message: 'messages.success.auth.password_updated',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage() ?: 'messages.errors.generic.operation_failed',
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateProfileImage(UpdateProfileImageRequest $updateProfileImageRequest): JsonResponse
    {
        try {
            $this->customerService->updateProfileImage($updateProfileImageRequest->file('image'));

            return $this->successResponse(
                message: 'messages.success.profile.image_updated',
                statusCode: Response::HTTP_OK,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }

    /**
     * Reset the customer's password.
     */
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        try {
            $this->userService->resetPassword($resetPasswordRequest->toDTO());

            return $this->successResponse(
                message: 'messages.success.password_reset',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }
}
