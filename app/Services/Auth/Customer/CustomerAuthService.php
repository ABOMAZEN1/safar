<?php

declare(strict_types=1);

namespace App\Services\Auth\Customer;

use Exception;
use App\Models\User;
use App\Models\Customer;
use App\Enum\UserTypeEnum;
use App\Services\OTP\OTPService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\CustomerRepository;

final readonly class CustomerAuthService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private OTPService $otpService,
    ) {}

    public function authenticateCustomer(array $credentials): array
    {
        $customer = $this->customerRepository->findByPhoneNumber($credentials['phone_number']);

        if (! Hash::check($credentials['password'], $customer->user->password)) {
            throw new Exception(
                __('messages.errors.auth.invalid_credentials'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        return [
            'token' => $customer->user->createToken('customer-token')->plainTextToken,
            'is_completed' => $customer->isProfileComplete(),
        ];
    }

    public function registerCustomer(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = User::create([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'password' => $data['password'],
                'type' => UserTypeEnum::CUSTOMER->value,
            ]);

            $this->customerRepository->createCustomer([
                'user_id' => $user->id,
            ]);

            return [
                'otp_code' => $this->otpService->generateAndSendCustomerOTP($user->phone_number),
            ];
        });
    }

    public function verify(array $verificationData): array
    {
        if (! $this->otpService->verifyCustomerOTP($verificationData['phone_number'], $verificationData['otp_code'])) {
            throw new Exception(
                __('messages.errors.auth.invalid_otp'),
                Response::HTTP_BAD_REQUEST
            );
        }

        $customer = $this->customerRepository->verifyPhone($verificationData['phone_number']);

        return [
            'token' => $customer->user->createToken('customer-token')->plainTextToken,
        ];
    }

    public function resendVerificationCode(array $data): array
    {
        return [
            'otp_code' => $this->otpService->generateAndSendCustomerOTP($data['phone_number']),
        ];
    }

    public function initiatePasswordReset(array $data): array
    {
        $this->customerRepository->findByPhoneNumber($data['phone_number']);

        return [
            'otp_code' => $this->otpService->generateAndSendCustomerOTP($data['phone_number']),
        ];
    }

    public function verifyUserVerificationCode(array $data): array
    {
        if (! $this->otpService->verifyCustomerOTP($data['phone_number'], $data['otp_code'])) {
            throw new Exception(
                __('messages.errors.auth.invalid_otp'),
                Response::HTTP_BAD_REQUEST
            );
        }

        $customer = $this->customerRepository->findByPhoneNumber($data['phone_number']);

        return [
            'token' => $customer->user->createToken('customer-token')->plainTextToken,
        ];
    }

    public function updatePassword(array $data): void
    {
        $customer = $this->customerRepository->findByPhoneNumber($data['phone_number']);

        if (isset($data['current_password']) && ! Hash::check($data['current_password'], $customer->user->password)) {
            throw new Exception(
                __('messages.errors.auth.invalid_credentials'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $this->customerRepository->updatePassword($data['phone_number'], $data['password']);
    }
}
