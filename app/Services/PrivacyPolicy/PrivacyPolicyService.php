<?php

declare(strict_types=1);

namespace App\Services\PrivacyPolicy;

use Illuminate\Support\Collection;
use App\Repositories\Eloquent\PrivacyPolicyRepository;

final readonly class PrivacyPolicyService
{
    public function __construct(
        private PrivacyPolicyRepository $privacyPolicyRepository,
    ) {}

    public function getPrivacyPolicy(): Collection
    {
        return $this->privacyPolicyRepository->getPrivacyPolicy();
    }
}
