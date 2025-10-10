<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\UserVerificationCode;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

final class UserVerificationCodeRepository
{
    /**
     * Returns a query builder instance for user verification codes filtered by user ID.
     */
    private function queryByUserId(int $userId): Builder
    {
        return UserVerificationCode::query()->where('user_id', $userId);
    }

    /**
     * Create a new user verification code.
     *
     * @param int             $otpCode   The one-time password code.
     * @param int             $userId    The ID of the user.
     * @param CarbonInterface $expiredAt The expiration time of the code.
     */
    public function createUserVerificationCode(int $otpCode, int $userId, CarbonInterface $expiredAt): void
    {
        UserVerificationCode::create([
            'code'       => $otpCode,
            'user_id'    => $userId,
            'expired_at' => $expiredAt,
        ]);
    }

    /**
     * Retrieve all user verification codes for a given user.
     *
     * @return Collection|null Returns a collection of codes or null if none exist.
     */
    public function getUserVerificationCodeByUserId(int $userId): ?Collection
    {
        $codes = $this->queryByUserId($userId)
            ->orderByDesc('created_at')
            ->get();

        return $codes->isNotEmpty() ? $codes : null;
    }

    /**
     * Delete all verification codes for a specific user.
     */
    public function deleteAllUserVerificationCodes(int $userId): void
    {
        $this->queryByUserId($userId)->delete();
    }

    /**
     * Retrieve the latest user verification code for a given user.
     *
     * @return UserVerificationCode|null The latest verification code or null if none exists
     */
    public function getUserVerificationCode(int $userId): ?UserVerificationCode
    {
        return $this->queryByUserId($userId)
            ->latest('created_at')
            ->firstOrFail();
    }
}
