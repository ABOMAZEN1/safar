<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\UserRole;
use Illuminate\Support\Collection;

final readonly class UserRoleRepository
{
    public function __construct(private UserRole $userRole) {}

    public function createUserRole(int $userId, int $roleId): void
    {
        $this->userRole->create([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }

    public function getUserRoles(int $userId): Collection
    {
        return $this->userRole->forUser($userId)
            ->with('role')
            ->get();
    }

    /**
     * Delete all roles for a specific user.
     *
     * @param int $userId The ID of the user whose roles should be deleted
     * @return int The number of records deleted
     */
    public function deleteByUserId(int $userId): int
    {
        return $this->userRole->where('user_id', $userId)->delete();
    }
}
