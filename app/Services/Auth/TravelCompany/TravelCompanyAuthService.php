<?php

declare(strict_types=1);

namespace App\Services\Auth\TravelCompany;

use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\TravelCompanyRepository;

final readonly class TravelCompanyAuthService
{
    public function __construct(
        private TravelCompanyRepository $travelCompanyRepository,
    ) {}

    /**
     * Authenticate a travel company user.
     *
     * @param  array<string, mixed> $credentials
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function authenticateTravelCompanyUser(array $credentials): array
    {
        $travelCompany = $this->travelCompanyRepository->findTravelCompanyByPhoneNumber(
            phoneNumber: $credentials['phone_number'],
        );

        if (! $travelCompany || ! Hash::check($credentials['password'], $travelCompany->user->password)) {
            throw new Exception(
                __('messages.errors.auth.invalid_credentials'),
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return [
            'token' => $travelCompany->user->createToken('travel-company-token')->plainTextToken,
            'travel_company' => $travelCompany,
        ];
    }
}
