<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BusDriver;
use Exception;
use Illuminate\Support\Facades\Log;

final class BusDriverObserver
{
    public function creating(BusDriver $busDriver): void
    {
        $this->validateUserNotAlreadyDriver($busDriver);
    }

    /**
     * Validate that the user is not already assigned as a driver.
     *
     * @param BusDriver $busDriver The bus driver to validate
     * @throws Exception If the user is already assigned as a driver
     */
    private function validateUserNotAlreadyDriver(BusDriver $busDriver): void
    {
        if (BusDriver::where('user_id', $busDriver->user_id)->exists()) {
            $errorMessage = __('messages.errors.generic.validation.failed');

            Log::error('BusDriver creation validation failed', [
                'user_id' => $busDriver->user_id,
                'travel_company_id' => $busDriver->travel_company_id,
                'error' => $errorMessage,
                'resolution' => 'Choose a different user who is not already assigned as a driver, or update the existing driver record instead of creating a new one.'
            ]);

            throw new Exception($errorMessage);
        }
    }
}
