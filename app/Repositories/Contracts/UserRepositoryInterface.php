<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array<string, mixed> $userData
     */
    public function createUser(array $userData): User;

    /**
     * Get a user by their phone number.
     */
    public function getUserByPhoneNumber(string $phoneNumber): ?User;

    /**
     * Set the verification timestamp for a user.
     */
    public function setVerifyAt(int $userId): int;

    /**
     * Update a user by their ID.
     *
     * @param array<string, mixed> $userData
     */
    public function updateUserById(int $userId, array $userData): int;

    /**
     * Find a user by phone number.
     */
    public function findByPhoneNumber(string $phoneNumber): ?User;

    /**
     * Update a user by ID.
     *
     * @param array<string, mixed> $data
     */
    public function updateById(int $id, array $data): User;
}
