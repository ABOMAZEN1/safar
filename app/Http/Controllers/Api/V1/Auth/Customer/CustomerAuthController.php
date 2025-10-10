<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\Customer;

use App\Services\Auth\Customer\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Auth\Customer\{
    CustomerLoginRequest,
    CustomerRegistrationRequest,
    CustomerVerifyAccountRequest,
    CustomerResendVerificationCodeRequest,
    CustomerInitiatePasswordResetRequest,
    CustomerVerifyPasswordResetCodeRequest,
};
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CustomerAuthController extends BaseApiController
{
    public function __construct(
        private readonly CustomerAuthService $customerAuthService,
    ) {}

    public function login(CustomerLoginRequest $customerLoginRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->authenticateCustomer($customerLoginRequest->validated());

            return $this->successResponse(
                message: __('messages.success.login'),
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $this->normalizeStatusCode($exception),
            );
        }
    }

    public function register(CustomerRegistrationRequest $customerRegistrationRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->registerCustomer($customerRegistrationRequest->validated());

            return $this->successResponse(
                message: __('messages.success.registration'),
                statusCode: Response::HTTP_CREATED,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $this->normalizeStatusCode($exception),
            );
        }
    }

    public function verify(CustomerVerifyAccountRequest $customerVerifyAccountRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->verify($customerVerifyAccountRequest->validated());

            return $this->successResponse(
                message: __('messages.success.account_verified'),
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Throwable $throwable) {
            return $this->errorResponse(
                message: $throwable->getMessage(),
                statusCode: $this->normalizeStatusCode($throwable),
                data: ['errors' => ['otp' => 'Invalid']],
            );
        }
    }

    public function resendVerification(CustomerResendVerificationCodeRequest $customerResendVerificationCodeRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->resendVerificationCode($customerResendVerificationCodeRequest->validated());

            return $this->successResponse(
                message: __('messages.success.otp_resent'),
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Throwable $throwable) {
            return $this->errorResponse(
                message: $throwable->getMessage(),
                statusCode: $this->normalizeStatusCode($throwable),
            );
        }
    }

    public function forgotPassword(CustomerInitiatePasswordResetRequest $customerInitiatePasswordResetRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->initiatePasswordReset($customerInitiatePasswordResetRequest->validated());

            return $this->successResponse(
                message: __('messages.success.password_reset_initiated'),
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Throwable $throwable) {
            return $this->errorResponse(
                message: $throwable->getMessage(),
                statusCode: $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function resetPasswordVerify(CustomerVerifyPasswordResetCodeRequest $customerVerifyPasswordResetCodeRequest): JsonResponse
    {
        try {
            $data = $this->customerAuthService->verifyUserVerificationCode($customerVerifyPasswordResetCodeRequest->validated());

            return $this->successResponse(
                message: __('messages.success.otp_verified'),
                statusCode: Response::HTTP_OK,
            );
        } catch (Throwable $throwable) {
            return $this->errorResponse(
                message: $throwable->getMessage(),
                statusCode: $this->normalizeStatusCode($throwable),
                data: ['errors' => ['otp' => 'Invalid']],
            );
        }
    }

    private function normalizeStatusCode(Throwable $throwable): int
    {
        $code = $throwable->getCode();

        if (is_int($code) && $code >= 100 && $code < 600) {
            return $code;
        }

        if (is_string($code) && ctype_digit($code)) {
            $intCode = (int) $code;
            if ($intCode >= 100 && $intCode < 600) {
                return $intCode;
            }
        }

        // Default to 500 Internal Server Error
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
