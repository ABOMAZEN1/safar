<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\TermsAndConditions;
use Illuminate\Support\Collection;

/**
 * Class TermsAndConditionsRepository.
 *
 * Handles the retrieval and management of terms and conditions.
 */
final readonly class TermsAndConditionsRepository
{
    public function __construct(
        private TermsAndConditions $termsAndConditions,
    ) {}

    /**
     * Get all terms and conditions.
     *
     * @return Collection<int, TermsAndConditions> Collection of terms and conditions
     */
    public function getAllTermsConditions(): Collection
    {
        return $this->termsAndConditions
            ->get();
    }
}
