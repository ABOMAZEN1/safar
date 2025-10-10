<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface PrivacyPolicyRepositoryInterface.
 */
interface PrivacyPolicyRepositoryInterface
{
    /**
     * Get the privacy policy.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getPrivacyPolicy(): Collection;
}
