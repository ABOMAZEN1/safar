<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\PrivacyPolicy;
use Illuminate\Support\Collection;

/**
 * Class PrivacyPolicyRepository.
 *
 * Handles the retrieval and management of privacy policies.
 */
final readonly class PrivacyPolicyRepository
{
    public function __construct(
        private PrivacyPolicy $privacyPolicy,
    ) {}

    /**
     * Get all privacy policies.
     *
     * @return Collection<int, PrivacyPolicy> Collection of privacy policies
     */
    public function getPrivacyPolicy(): Collection
    {
        return $this->privacyPolicy
            ->get();
    }
}
