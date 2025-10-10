<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusDriver;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\BusDriver\CreateDriverRequest;
use App\Http\Requests\Api\V1\BusDriver\EditDriverRequest;
use App\Http\Requests\Api\V1\User\ResetPasswordRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use App\Http\Resources\Api\V1\BusDriver\BusDriverResource;
use App\Models\User;
use App\Services\BusDriver\DriverService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class BusDriverController extends BaseApiController
{
    public function __construct(
        private readonly DriverService $driverService,
        private readonly UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            /** @var null|User $user */
            $user = Auth::user();

            $drivers = $this->driverService->getCompanyDrivers($user->company->id)
                ->load(['user']);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: BusDriverResource::collection($drivers),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function store(CreateDriverRequest $createDriverRequest): JsonResponse
    {
        try {
            $driver = $this->driverService->create($createDriverRequest->validated());

            return $this->successResponse(
                message: __('messages.success.created'),
                data: new BusDriverResource($driver->load(['user', 'travelCompany'])),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed') . ' ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(int $id, EditDriverRequest $editDriverRequest): JsonResponse
    {
        try {
            $driver = $this->driverService->edit($id, $editDriverRequest->validated());

            return $this->successResponse(
                message: __('messages.success.updated'),
                data: new BusDriverResource($driver->load(['user', 'travelCompany'])),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function profile(): JsonResponse
    {
        try {
            /** @var null|User $driver */
            $driver = Auth::user();

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: new UserResource($driver),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Reset the bus driver's password.
     */
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): JsonResponse
    {
        try {
            $this->userService->resetPassword($resetPasswordRequest->toDTO());

            return $this->successResponse(
                message: __('messages.success.password_reset'),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed') . ' ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->driverService->destroy($id);

            return $this->successResponse(
                message: __('messages.success.deleted'),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed') . ' ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
