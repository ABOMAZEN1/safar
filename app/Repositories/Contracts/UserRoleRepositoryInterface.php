<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use App\Models\UserRole;

/**
 * Interface UserRoleRepositoryInterface.
 */
interface UserRoleRepositoryInterface
{
    /**
     * Create a user role.
     */
    public function createUserRole(int $userId, int $roleId): void;

    /**
     * Get user roles.
     *
     * @return Collection<int, UserRole>
     */
    public function getUserRoles(int $userId): Collection;
}
