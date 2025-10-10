<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\TravelCompany;
use Illuminate\Support\Collection;

/**
 * Interface TravelCompanyRepositoryInterface.
 */
interface TravelCompanyRepositoryInterface
{
    /**
     * Get active travel companies.
     *
     * @return Collection<int, TravelCompany>
     */
    public function getActiveTravelCompanies(): Collection;

    /**
     * Get travel company information by user ID.
     *
     * @param  int                $userId The user ID to search for
     * @return TravelCompany|null The matching travel company or null if not found
     */
    public function getTravelCompanyInformationByUserId(int $userId): ?TravelCompany;

    /**
     * Find travel company by phone number.
     *
     * @param  string             $phoneNumber The phone number to search for
     * @return TravelCompany|null The matching travel company or null if not found
     */
    public function findTravelCompanyByPhoneNumber(string $phoneNumber): ?TravelCompany;

    public function findById(int $id): ?TravelCompany;
}
