<?php

declare(strict_types=1);

namespace App\Services\User;

use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\UserRepository;
use App\DataTransferObjects\User\ResetPasswordDto;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * Reset a user's password.
     *
     * @throws Exception
     */
    public function resetPassword(ResetPasswordDto $resetPasswordDto): void
    {
        $user = $this->userRepository->findByPhoneNumber($resetPasswordDto->phone_number);
        
        if (!Hash::check($resetPasswordDto->current_password, $user->password)) {
            throw new Exception(
                trans('messages.errors.auth.invalid_credentials'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $this->userRepository->updateById($user->id, [
            'password' => Hash::make($resetPasswordDto->new_password),
        ]);
    }
}
