<?php

declare(strict_types=1);

namespace App\Services\BusDriver;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\BusDriver;
use App\Enum\UserTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Eloquent\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\UnauthorizedException;
use App\Repositories\Eloquent\UserRoleRepository;
use App\Repositories\Eloquent\BusDriverRepository;

final readonly class DriverService
{
    public function __construct(
        private BusDriverRepository $busDriverRepository,
        private UserRepository $userRepository,
        private UserRoleRepository $userRoleRepository,
    ) {}

    public function getCompanyDrivers(int $companyId): Collection
    {
        return $this->busDriverRepository->getCompanyDrivers($companyId);
    }

    public function create(array $data): BusDriver
    {
        return DB::transaction(function () use ($data): BusDriver {
            $user = $this->userRepository->createUser([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'password' => $data['password'],
                'verified_at' => now(),
                'type' => UserTypeEnum::BUS_DRIVER->value,
            ]);

            $driverRole = Role::where('role_name', UserTypeEnum::BUS_DRIVER->value)->firstOrFail();
            $customerRole = Role::where('role_name', UserTypeEnum::CUSTOMER->value)->firstOrFail();

            $this->userRoleRepository->createUserRole($user->id, $driverRole->id);
            $this->userRoleRepository->createUserRole($user->id, $customerRole->id);

            /** @var null|User $authUser */
            $authUser = Auth::user();

            return $this->busDriverRepository->createBusDriver([
                'user_id' => $user->id,
                'travel_company_id' => $authUser->company->id,
            ]);
        });
    }

    /**
     * Edit a bus driver with validation logic.
     *
     * @param int $id The ID of the bus driver
     * @param array $data The data to update
     * @return BusDriver The updated bus driver
     * @throws Exception If validation fails or driver not found
     */
    public function edit(int $id, array $data): BusDriver
    {
        return DB::transaction(function () use ($id, $data): BusDriver {
            // Get the driver and verify it exists
            try {
                $driver = $this->busDriverRepository->getBusDriver($id);
            } catch (Exception) {
                throw new Exception(
                    message: __('messages.errors.generic.not_found'),
                    code: Response::HTTP_NOT_FOUND
                );
            }

            // Verify the authenticated user can edit this driver
            /** @var null|User $authUser */
            $authUser = Auth::user();
            if ($driver->travel_company_id !== $authUser->company->id) {
                throw new Exception(
                    message: __('messages.errors.generic.unauthorized'),
                    code: Response::HTTP_FORBIDDEN
                );
            }

            // Validate phone number uniqueness if it's being changed
            if (isset($data['phone_number'])) {
                $existingUser = User::where('phone_number', $data['phone_number'])
                    ->where('type', UserTypeEnum::BUS_DRIVER->value)
                    ->where('id', '!=', $driver->user_id)
                    ->first();

                if ($existingUser) {
                    throw new Exception(
                        message: __('messages.errors.generic.validation.failed') . ': Phone number already in use',
                        code: Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
            }

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Update the user
            $this->userRepository->updateUserById($driver->user_id, $data);

            // Return the refreshed driver with relationships
            return $driver->fresh(['user', 'travelCompany']);
        });
    }

    public function destroy(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $driver = $this->busDriverRepository->getBusDriver($id);

            /** @var null|User $user */
            $user = Auth::user();

            if ($driver->travel_company_id !== $user->company->id) {
                throw new UnauthorizedException('You are not authorized to delete this driver');
            }

            $userId = $driver->user_id;

            $this->busDriverRepository->deleteBusDriver($id);

            $this->userRoleRepository->deleteByUserId($userId);
            $this->userRepository->deleteById($userId);
        });
    }
}
