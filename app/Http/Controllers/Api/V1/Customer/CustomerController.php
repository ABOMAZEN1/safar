<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Customer;

use App\Models\User;
use Exception;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Customer\CustomerService;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\Customer\CustomerResource;
use App\Http\Requests\Api\V1\Customer\UpdateInformationRequest;
use App\Http\Requests\Api\V1\Customer\StoreCustomerInformationRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class CustomerController extends BaseApiController
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function store(StoreCustomerInformationRequest $storeCustomerInformationRequest): JsonResponse
    {
        try {
            $this->customerService->createCustomer($storeCustomerInformationRequest->validated());

            return $this->successResponse(
                message: 'messages.success.created',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }

    public function update(UpdateInformationRequest $updateInformationRequest): JsonResponse
    {
        try {
            $dto = $updateInformationRequest->toDTO();
            $this->customerService->updateCustomerInformation($dto);

            /** @var User $user */
            $user = Auth::user();
            $customer = $user->customer;

            if (!$customer) {
                return $this->errorResponse(
                    message: 'messages.errors.auth.customer_not_found',
                    statusCode: Response::HTTP_NOT_FOUND,
                );
            }

            $customer->refresh();
            $isProfileComplete = $customer->isProfileComplete();
            $response = [
                'is_profile_complete' => $isProfileComplete,
            ];

            if (!$isProfileComplete) {
                $response['missing_fields'] = $customer->getMissingFields();
            }

            return $this->successResponse(
                message: 'messages.success.updated',
                data: $response,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }

    public function getProfile(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $customer = $this->customerService->getProfile($user->id);

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: [
                    'customer' => CustomerResource::make($customer->load('user')),
                ],
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse(
                message: 'messages.errors.auth.customer_not_found',
                statusCode: Response::HTTP_NOT_FOUND,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.server_error',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }
}
