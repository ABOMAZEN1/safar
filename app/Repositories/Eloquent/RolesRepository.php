<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enum\UserTypeEnum;
use App\Models\Role;
use Illuminate\Support\Collection;

/**
 * Class RolesRepository.
 *
 * Handles the retrieval and management of roles.
 */
final readonly class RolesRepository
{
    public function __construct(
        private Role $role,
    ) {}

    /**
     * Get the customer role ID.
     *
     * @return int The ID of the customer role
     */
    public function getCustomerRoleId(): int
    {
        return $this->role
            ->where('name', UserTypeEnum::CUSTOMER->value)
            ->value('id');
    }

    /**
     * Get all roles.
     *
     * @return Collection<int, Role> Collection of roles
     */
    public function getAllRoles(): Collection
    {
        return $this->role
            ->get();
    }
}
