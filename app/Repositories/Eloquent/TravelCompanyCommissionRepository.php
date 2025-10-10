<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\TravelCompanyCommission;

/**
 * Class TravelCompanyCommissionRepository.
 *
 * Handles the creation, retrieval, and management of travel company commissions.
 *
 */
final readonly class TravelCompanyCommissionRepository
{
    public function __construct(
        private TravelCompanyCommission $travelCompanyCommission,
    ) {}

    /**
     * Get the current commission for a company.
     *
     * @param  int                          $companyId The company ID
     * @return TravelCompanyCommission|null The commission if found, null otherwise
     */
    public function getTravelCompanyCommissionByCompanyId(int $companyId): ?TravelCompanyCommission
    {
        return $this->travelCompanyCommission
            ->where('travel_company_id', $companyId)
            ->latest()
            ->firstOrFail();
    }

    /**
     * Create a new commission.
     *
     * @param  array<string, mixed>    $data The commission data
     * @return TravelCompanyCommission The created commission
     */
    public function createTravelCompanyCommission(array $data): TravelCompanyCommission
    {
        return $this->travelCompanyCommission->create($data);
    }

    /**
     * Update commission.
     *
     * @param TravelCompanyCommission $travelCompanyCommission The commission to update
     * @param array<string, mixed>    $data                    The update data
     */
    public function updateTravelCompanyCommission(TravelCompanyCommission $travelCompanyCommission, array $data): void
    {
        $travelCompanyCommission->update($data);
    }
}
