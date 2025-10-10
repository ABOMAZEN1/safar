<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TravelCompany;
use App\Models\TravelCompanyCommission;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class TravelCompanyCommissionObserver
{
    public function creating(TravelCompanyCommission $travelCompanyCommission): void
    {
        $this->validatePositiveCommissionAmount($travelCompanyCommission);
        $this->validateTravelCompanyExists($travelCompanyCommission);
    }

    public function updating(TravelCompanyCommission $travelCompanyCommission): void
    {
        $this->validatePositiveCommissionAmount($travelCompanyCommission);
        $this->validateTravelCompanyExists($travelCompanyCommission);
    }

    /**
     * Validate that the commission amount is positive.
     *
     * @param TravelCompanyCommission $travelCompanyCommission The commission to validate
     * @throws Exception If the commission amount is not positive
     */
    private function validatePositiveCommissionAmount(TravelCompanyCommission $travelCompanyCommission): void
    {
        $commissionAmount = $travelCompanyCommission->commission_amount;

        if ($commissionAmount <= 0) {
            $errorMessage = __('messages.errors.generic.validation.failed');

            Log::error('TravelCompanyCommission validation failed: Non-positive commission amount', [
                'commission_id' => $travelCompanyCommission->id ?? 'new_commission',
                'company_id' => $travelCompanyCommission->travel_company_id,
                'commission_amount' => $commissionAmount,
                'error' => $errorMessage,
                'resolution' => 'Ensure the commission amount is greater than zero.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate that the travel company exists.
     *
     * @param TravelCompanyCommission $travelCompanyCommission The commission to validate
     * @throws Exception If the travel company does not exist
     */
    private function validateTravelCompanyExists(TravelCompanyCommission $travelCompanyCommission): void
    {
        try {
            TravelCompany::findOrFail($travelCompanyCommission->travel_company_id);
        } catch (ModelNotFoundException) {
            Log::error('TravelCompanyCommission validation failed: Company not found', [
                'commission_id' => $travelCompanyCommission->id ?? 'new_commission',
                'company_id' => $travelCompanyCommission->travel_company_id,
                'error' => __('messages.errors.auth.company_not_found'),
                'resolution' => 'Select an existing travel company or create one before setting a commission.'
            ]);

            throw new Exception(__('messages.errors.auth.company_not_found'));
        }
    }
}
