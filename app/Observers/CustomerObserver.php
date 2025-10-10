<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Customer;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use function strlen;

final class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        $this->validateNationalIdLength($customer);
        $this->validateBirthDateNotInFuture($customer);
    }

    public function updating(Customer $customer): void
    {
        $this->validateNationalIdLength($customer);
        $this->validateBirthDateNotInFuture($customer);
    }

    /**
     * Validate that the national ID is exactly 11 digits.
     *
     * @param Customer $customer The customer to validate
     * @throws Exception If the national ID length is invalid
     */
    private function validateNationalIdLength(Customer $customer): void
    {
        $nationalId = $customer->national_id;

        if ($nationalId !== null && $nationalId !== '' && strlen($nationalId) !== 11) {
            $errorMessage = __('messages.errors.generic.validation.failed');

            Log::error('Customer validation failed: Invalid national ID length', [
                'customer_id' => $customer->id ?? 'new_customer',
                'national_id_length' => strlen($nationalId),
                'error' => $errorMessage,
                'resolution' => 'Ensure the national ID is exactly 11 digits long.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate that the birth date is not in the future.
     *
     * @param Customer $customer The customer to validate
     * @throws Exception If the birth date is in the future
     */
    private function validateBirthDateNotInFuture(Customer $customer): void
    {
        $birthDate = $customer->birth_date;

        if ($birthDate !== null && $birthDate !== '' && $this->isDateInFuture($birthDate)) {
            $errorMessage = __('messages.errors.generic.validation.failed');

            Log::error('Customer validation failed: Future birth date', [
                'customer_id' => $customer->id ?? 'new_customer',
                'birth_date' => $birthDate,
                'error' => $errorMessage,
                'resolution' => 'Select a birth date that is today or in the past.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Check if a date is in the future.
     *
     * @param string|Carbon|null $date The date to check
     * @return bool True if the date is in the future, false otherwise
     */
    private function isDateInFuture($date): bool
    {
        if ($date === null) {
            return false;
        }

        if ($date instanceof Carbon) {
            return $date->isAfter(Carbon::today());
        }

        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
        return $carbonDate->isAfter(Carbon::today());
    }
}
