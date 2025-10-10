<?php

declare(strict_types=1);

namespace App\Services\OTP;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\BusDriver;
use App\Models\UserVerificationCode;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\BusDriverRepository;
use App\Repositories\Eloquent\UserVerificationCodeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final readonly class OTPService
{
    public function __construct(
        private UserVerificationCodeRepository $userVerificationCodeRepository,
        private CustomerRepository $customerRepository,
        private BusDriverRepository $busDriverRepository,
    ) {}

    /**
     * Generate and send OTP code for a customer.
     *
     * @param string $phoneNumber The customer's phone number
     * @return int The generated OTP code
     * @throws Exception If customer not found
     */
    public function generateAndSendCustomerOTP(string $phoneNumber): int
    {
        try {
            $customer = $this->customerRepository->findByPhoneNumber($phoneNumber);

            return $this->createOTPCode($customer->user->id);
        } catch (ModelNotFoundException) {
            throw new Exception(
                __('messages.errors.auth.customer_not_found'),
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Generate and send OTP code for a bus driver.
     *
     * @param string $phoneNumber The bus driver's phone number
     * @return int The generated OTP code
     * @throws Exception If bus driver not found
     */
    public function generateAndSendDriverOTP(string $phoneNumber): int
    {
        try {
            $busDriver = $this->busDriverRepository->findBusDriverByPhoneNumber($phoneNumber);

            return $this->createOTPCode($busDriver->user->id);
        } catch (ModelNotFoundException) {
            throw new Exception(
                __('messages.errors.auth.user_not_found'),
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Create and store an OTP code for a user.
     *
     * @param int $userId The user ID
     * @return int The generated OTP code
     */
    private function createOTPCode(int $userId): int
    {
        $otpCode = random_int(1000, 9999);

        $this->userVerificationCodeRepository->createUserVerificationCode(
            otpCode: $otpCode,
            userId: $userId,
            expiredAt: Carbon::now()->addMinutes(10),
        );

        return $otpCode;
    }

    /**
     * Verify an OTP code for a customer.
     *
     * @param string $phoneNumber The customer's phone number
     * @param int $otpCode The OTP code to verify
     * @return bool True if verification is successful
     * @throws Exception If verification fails
     */
    public function verifyCustomerOTP(string $phoneNumber, int $otpCode): bool
    {
        try {
            $customer = $this->customerRepository->findByPhoneNumber($phoneNumber);

            return $this->verifyOTPCode($customer->user->id, $otpCode);
        } catch (ModelNotFoundException) {
            throw new Exception(
                __('messages.errors.auth.customer_not_found'),
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Verify an OTP code for a bus driver.
     *
     * @param string $phoneNumber The bus driver's phone number
     * @param int $otpCode The OTP code to verify
     * @return bool True if verification is successful
     * @throws Exception If verification fails
     */
    public function verifyDriverOTP(string $phoneNumber, int $otpCode): bool
    {
        try {
            $busDriver = $this->busDriverRepository->findBusDriverByPhoneNumber($phoneNumber);

            return $this->verifyOTPCode($busDriver->user->id, $otpCode);
        } catch (ModelNotFoundException) {
            throw new Exception(
                __('messages.errors.auth.user_not_found'),
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Verify an OTP code for a user.
     *
     * @param int $userId The user ID
     * @param int $otpCode The OTP code to verify
     * @return bool True if verification is successful
     * @throws Exception If verification fails
     */
    private function verifyOTPCode(int $userId, int $otpCode): bool
    {
        $storedCode = $this->userVerificationCodeRepository->getUserVerificationCode($userId);

        if (!$storedCode instanceof UserVerificationCode) {
            throw new Exception(
                __('messages.errors.auth.wrong_code'),
                Response::HTTP_BAD_REQUEST,
            );
        }

        if ($storedCode->code !== $otpCode) {
            throw new Exception(
                __('messages.errors.auth.wrong_code'),
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (Carbon::parse($storedCode->expired_at)->isPast()) {
            throw new Exception(
                __('messages.errors.auth.code_expired'),
                Response::HTTP_BAD_REQUEST,
            );
        }

        return true;
    }
}
