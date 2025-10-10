<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\TravelCompanyCommission;

interface TravelCompanyCommissionRepositoryInterface
{
    /**
     * Get the current commission for a company.
     */
    public function getTravelCompanyCommissionByCompanyId(int $companyId): ?TravelCompanyCommission;

    /**
     * Create a new commission.
     *
     * @param array<string, mixed> $data
     */
    public function createTravelCompanyCommission(array $data): TravelCompanyCommission;

    /**
     * Update commission.
     *
     * @param array<string, mixed> $data
     */
    public function updateTravelCompanyCommission(TravelCompanyCommission $travelCompanyCommission, array $data): void;
}
