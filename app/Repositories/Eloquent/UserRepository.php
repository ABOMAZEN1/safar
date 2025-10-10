<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserRepository
{
    public function __construct(private User $user) {}

    public function createUser(array $userData): User
    {
        return $this->user->create($userData);
    }

    public function getUserByPhoneNumber(string $phoneNumber): ?User
    {
        return $this->user->where('phone_number', $phoneNumber)
            ->firstOrFail();
    }

    public function setVerifyAt(int $userId): int
    {
        return $this->user->where('id', $userId)->update(['verified_at' => now()]);
    }

    /**
     * Update a user by ID with improved error handling.
     *
     * @param int $userId The ID of the user to update
     * @param array $userData The data to update the user with
     * @return int The number of affected rows
     * @throws Exception If the user is not found
     */
    public function updateUserById(int $userId, array $userData): int
    {
        try {
            $user = $this->user->findOrFail($userId);

            return $this->user->where('id', $userId)->update($userData);
        } catch (ModelNotFoundException) {
            throw new Exception(
                message: __('messages.errors.auth.user_not_found'),
                code: Response::HTTP_NOT_FOUND
            );
        } catch (Exception $e) {
            throw new Exception(message: __('messages.errors.generic.operation_failed') . ': ' . $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR, previous: $e);
        }
    }

    public function findByPhoneNumber(string $phoneNumber): ?User
    {
        return $this->user->where('phone_number', $phoneNumber)->firstOrFail();
    }

    /**
     * Update a user by ID and return the updated user.
     *
     * @param int $id The ID of the user to update
     * @param array $data The data to update the user with
     * @return User The updated user
     * @throws ModelNotFoundException If the user is not found
     */
    public function updateById(int $id, array $data): User
    {
        $user = $this->user->findOrFail($id);

        $user->fill($data);
        $user->save();

        return $user->fresh();
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id The ID of the user to delete
     * @return bool Whether the deletion was successful
     * @throws ModelNotFoundException If the user is not found
     */
    public function deleteById(int $id): bool
    {
        return $this->user->findOrFail($id)->delete();
    }
}
