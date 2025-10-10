<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Support\Collection;

/**
 * Interface RolesRepositoryInterface.
 */
interface RolesRepositoryInterface
{
    /**
     * Get the customer role ID.
     */
    public function getCustomerRoleId(): int;

    /**
     * Get all roles.
     *
     * @return Collection<int, Role>
     */
    public function getAllRoles(): Collection;
}
