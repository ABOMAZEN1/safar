<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Customer;

use Exception;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Auth\Customer\CustomerAuthService;
use App\Http\Requests\Api\V1\Customer\UpdatePasswordRequest;
use App\Http\Requests\Api\V1\Customer\UpdateProfileImageRequest;
use App\Http\Requests\Api\V1\Customer\UpdateCustomerProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Customer\CustomerService;
use App\Http\Requests\Api\V1\User\ResetPasswordRequest;
use App\Services\User\UserService;

final class CustomerAccountController extends BaseApiController
{
    public function __construct(
        private readonly CustomerAuthService $customerAuthService,
        private readonly CustomerService $customerService,
        private readonly UserService $userService,
    ) {}

    /**
     * Get customer profile
     */
    public function profile(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user || !$user->customer) {
                return $this->errorResponse(
                    message: 'العميل غير موجود',
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }

            $customer = $user->customer;

            return $this->successResponse(
                message: 'تم جلب بيانات العميل بنجاح',
                data: [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone_number' => $user->phone_number,
                        'profile_image' => $user->profile_image_path ? url('storage/' . $user->profile_image_path) : null,
                        'verified_at' => $user->verified_at,
                        'created_at' => $user->created_at,
                    ],
                    'customer' => [
                        'id' => $customer->id,
                        'birth_date' => $customer->birth_date,
                        'national_id' => $customer->national_id,
                        'gender' => $customer->gender,
                        'address' => $customer->address,
                        'mother_name' => $customer->mother_name,
                        'is_profile_complete' => $customer->isProfileComplete(),
                        'missing_fields' => $customer->getMissingFields(),
                        'updated_at' => $customer->updated_at,
                    ],
                ],
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update customer profile
     */
    public function updateProfile(UpdateCustomerProfileRequest $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user || !$user->customer) {
                return $this->errorResponse(
                    message: 'العميل غير موجود',
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }

            $data = $request->validated();

            return DB::transaction(function () use ($user, $data) {
                // تحديث بيانات المستخدم
                $userData = array_filter($data, function ($key) {
                    return in_array($key, ['name', 'phone_number', 'firebase_token']);
                }, ARRAY_FILTER_USE_KEY);

                if (!empty($userData)) {
                    // تحديث Firebase token مع timestamp
                    if (isset($userData['firebase_token'])) {
                        $userData['firebase_token_updated_at'] = now();
                    }

                    $user->update($userData);
                }

                // تحديث بيانات العميل
                $customerData = array_filter($data, function ($key) {
                    return in_array($key, ['birth_date', 'national_id', 'gender', 'address', 'mother_name']);
                }, ARRAY_FILTER_USE_KEY);

                if (!empty($customerData)) {
                    $user->customer->update($customerData);
                }

                // إعادة تحميل البيانات
                $user = $user->fresh(['customer']);

                return $this->successResponse(
                    message: 'تم تحديث بيانات العميل بنجاح',
                    data: [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'phone_number' => $user->phone_number,
                            'profile_image' => $user->profile_image_path ? url('storage/' . $user->profile_image_path) : null,
                            'updated_at' => $user->updated_at,
                        ],
                        'customer' => [
                            'id' => $user->customer->id,
                            'birth_date' => $user->customer->birth_date,
                            'national_id' => $user->customer->national_id,
                            'gender' => $user->customer->gender,
                            'address' => $user->customer->address,
                            'mother_name' => $user->customer->mother_name,
                            'is_profile_complete' => $user->customer->isProfileComplete(),
                            'missing_fields' => $user->customer->getMissingFields(),
                            'updated_at' => $user->customer->updated_at,
                        ],
                    ],
                );
            });
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updatePassword(UpdatePasswordRequest $updatePasswordRequest): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (! $user) {
                return $this->errorResponse(
                    message: 'messages.errors.generic.unauthorized',
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }

            $this->customerAuthService->updatePassword([
                'phone_number' => $user->phone_number,
                'current_password' => $updatePasswordRequest->validated('current_password'),
                'password' => $updatePasswordRequest->validated('password'),
            ]);

            return $this->successResponse(
                message: 'messages.success.auth.password_updated',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage() ?: 'messages.errors.generic.operation_failed',
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function updateProfileImage(UpdateProfileImageRequest $updateProfileImageRequest): JsonResponse
    {
        try {
            $this->customerService->updateProfileImage($updateProfileImageRequest->file('image'));

            return $this->successResponse(
                message: 'messages.success.profile.image_updated',
                statusCode: Response::HTTP_OK,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }

    /**
     * Reset the customer's password.
     */
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        try {
            $this->userService->resetPassword($resetPasswordRequest->toDTO());

            return $this->successResponse(
                message: 'messages.success.password_reset',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }
}
