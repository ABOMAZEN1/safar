<?php

declare(strict_types=1);

namespace App\Services\Auth\BusDriver;

use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusDriverRepository;

final readonly class BusDriverAuthService
{
    public function __construct(
        private BusDriverRepository $busDriverRepository,
    ) {}

    /**
     * Authenticate a bus driver.
     *
     * @param  array<string, mixed> $credentials
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function authenticateBusDriver(array $credentials): array
    {
        $busDriver = $this->busDriverRepository->findBusDriverByPhoneNumber(
            phoneNumber: $credentials['phone_number'],
        );

        if (! Hash::check($credentials['password'], $busDriver->user->password)) {
            throw new Exception(
                __('messages.errors.auth.invalid_credentials'),
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return [
            'access_token' => $busDriver->user->createToken('bus-driver-token')->plainTextToken,
            'bus_driver' => $busDriver,
        ];
    }
}
