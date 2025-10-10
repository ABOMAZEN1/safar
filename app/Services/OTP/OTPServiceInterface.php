<?php

declare(strict_types=1);

namespace App\Services\OTP;

interface OTPServiceInterface
{
    public function generateAndSend(string $phoneNumber): int;

    public function verify(string $phoneNumber, int $code): bool;
}
