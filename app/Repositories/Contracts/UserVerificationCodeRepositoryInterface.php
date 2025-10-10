<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use App\Models\UserVerificationCode;

/**
 * Interface UserVerificationCodeRepositoryInterface.
 */
interface UserVerificationCodeRepositoryInterface
{
    /**
     * Create a new user verification code.
     */
    public function createUserVerificationCode(int $otpCode, int $userId, CarbonInterface $expiredAt): void;

    /**
     * Get user verification codes by user ID.
     *
     * @return Collection<int, UserVerificationCode>|null
     */
    public function getUserVerificationCodeByUserId(int $userId): ?Collection;

    /**
     * Delete all user verification codes for a user.
     */
    public function deleteAllUserVerificationCodes(int $userId): void;

    public function getUserVerificationCode(int $userId): ?array;
}
