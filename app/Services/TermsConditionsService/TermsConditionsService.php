<?php

declare(strict_types=1);

namespace App\Services\TermsConditionsService;

use Illuminate\Support\Collection;
use App\Repositories\Eloquent\TermsAndConditionsRepository;

final readonly class TermsConditionsService
{
    public function __construct(
        private TermsAndConditionsRepository $termsAndConditionsRepository,
    ) {}

    public function getTermsConditions(): Collection
    {
        return $this->termsAndConditionsRepository->getAllTermsConditions();
    }
}
