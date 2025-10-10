<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use App\Models\TermsAndConditions;

/**
 * Interface TermsAndConditionsRepositoryInterface.
 */
interface TermsAndConditionsRepositoryInterface
{
    /**
     * Retrieve all terms and conditions.
     *
     * @return Collection<int, TermsAndConditions>
     */
    public function getAllTermsConditions(): Collection;
}
