<?php

declare(strict_types=1);

namespace App\Services\Customer;

use Exception;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\DataTransferObjects\Customer\CreateCustomerDto;
use App\DataTransferObjects\Customer\UpdateCustomerInformationDto;

final readonly class CustomerService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private UserRepository $userRepository,
    ) {}

    public function createCustomer(array $data): void
    {
        /** @var null|User $user */
        $user = Auth::user();

        if ($user->customer) {
            throw new Exception(
                'already entered Info',
                Response::HTTP_CONFLICT,
            );
        }

        $createCustomerDto = CreateCustomerDto::fromArray($data);
        $data = array_merge($createCustomerDto->toArray(), ['user_id' => $user->id]);

        $this->customerRepository->createCustomer($data);
    }

    public function updateCustomerInformation(UpdateCustomerInformationDto $updateCustomerInformationDto): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->customer) {
            throw new Exception(
                'Customer profile not found',
                Response::HTTP_NOT_FOUND,
            );
        }

        DB::transaction(function () use ($updateCustomerInformationDto, $user): void {
            if ($updateCustomerInformationDto->name !== null) {
                $this->userRepository->updateUserById(
                    $user->id,
                    ['name' => $updateCustomerInformationDto->name],
                );
            }

            $customerData = array_filter([
                'birth_date' => $updateCustomerInformationDto->birth_date,
                'national_id' => $updateCustomerInformationDto->national_id,
                'gender' => $updateCustomerInformationDto->gender,
                'address' => $updateCustomerInformationDto->address,
                'mother_name' => $updateCustomerInformationDto->mother_name,
            ], fn($value): bool => $value !== null);

            if ($customerData !== []) {
                $this->customerRepository->updateCustomerById($user->customer->id, $customerData);
            }
        });
    }

    public function getProfile(int $userId): Customer
    {
        return $this->customerRepository->getCustomerByUserIdOrFail($userId);
    }

    public function updateProfileImage(UploadedFile $uploadedFile): void
    {
        /** @var null|User $user */
        $user = Auth::user();

        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->getRawOriginal('profile_image_path'));
        }

        $path = $uploadedFile->store('profile-images', 'public');

        $this->userRepository->updateUserById(
            $user->id,
            ['profile_image_path' => $path]
        );
    }
}
