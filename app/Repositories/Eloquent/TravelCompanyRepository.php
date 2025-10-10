<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\TravelCompany;
use Illuminate\Support\Collection;
use App\DataTransferObjects\TravelCompany\CompanyFilterDTO;

final readonly class TravelCompanyRepository
{
    public function __construct(
        private TravelCompany $travelCompany,
    ) {}

    /**
     * Get all active travel companies with optional filtering.
     *
     * @param CompanyFilterDTO|null $companyFilterDTO Optional filter criteria
     * @return Collection<int, TravelCompany> Collection of filtered travel companies
     */
    public function getActiveTravelCompanies(?CompanyFilterDTO $companyFilterDTO): Collection
    {
        $query = $this->travelCompany->query();

        // Apply filters if provided
        // Filter by name
        if ($companyFilterDTO->name !== null) {
            $query->where('company_name', 'like', '%' . $companyFilterDTO->name . '%');
        }

        // Filter by address
        if ($companyFilterDTO->address !== null) {
            $query->where('address', 'like', '%' . $companyFilterDTO->address . '%');
        }

        // Filter by companies that have buses
        if ($companyFilterDTO->hasBuses === true) {
            $query->has('buses');
        }

        // Filter by companies that have active trips
        if ($companyFilterDTO->hasActiveTrips === true) {
            $query->whereHas('busTrips', function ($q): void {
                $q->where('departure_datetime', '>', now());
            });
        }

        // Apply ordering
        if ($companyFilterDTO->orderBy !== null) {
            match ($companyFilterDTO->orderBy) {
                'name_asc' => $query->orderBy('company_name', 'asc'),
                'name_desc' => $query->orderBy('company_name', 'desc'),
                default => $query->orderBy('company_name', 'asc'),
            };
        }


        return $query->get();
    }

    /**
     * Get travel company information by user ID.
     *
     * @param  int                $userId The user ID
     * @return TravelCompany|null The travel company if found, null otherwise
     */
    public function getTravelCompanyInformationByUserId(int $userId): ?TravelCompany
    {
        return $this->travelCompany
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function findTravelCompanyByPhoneNumber(string $phoneNumber): ?TravelCompany
    {
        return $this->travelCompany
            ->whereHas('user', function ($query) use ($phoneNumber): void {
                $query->where('phone_number', $phoneNumber);
            })
            ->firstOrFail();
    }

    public function findTravelCompanyByIdOrFail(int $id): TravelCompany
    {
        return $this->travelCompany
            ->findOrFail($id);
    }
}
