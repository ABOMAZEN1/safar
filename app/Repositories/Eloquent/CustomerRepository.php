<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Customer;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * Class CustomerRepository.
 *
 * Handles the creation and retrieval of customers.
 */
final readonly class CustomerRepository
{
    public function __construct(
        private Customer $customer,
    ) {}

    /**
     * Create a new customer.
     *
     * @param  array<string, mixed> $createCustomerData The customer attributes
     * @return Customer             The created customer
     */
    public function createCustomer(array $createCustomerData): Customer
    {
        return $this->customer->create($createCustomerData);
    }

    /**
     * Get a customer by the user ID or fail.
     *
     * @param  int      $userId The user ID
     * @return Customer The customer if found
     * @throws ModelNotFoundException If customer not found
     */
    public function getCustomerByUserIdOrFail(int $userId): Customer
    {
        return $this->customer
            ->with('user')
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Update a customer by ID.
     *
     * @param  int                  $customerId         The ID of the customer to update
     * @param  array<string, mixed> $updateCustomerData The data to update the customer with
     * @return Customer             The updated customer
     */
    public function updateCustomerById(int $customerId, array $updateCustomerData): Customer
    {
        $customer = $this->customer->findOrFail($customerId);
        $customer->update($updateCustomerData);
        return $customer->refresh();
    }

    /**
     * Find a customer by phone number.
     *
     * @param string $phoneNumber The phone number to search for
     * @return Customer The customer if found
     * @throws ModelNotFoundException If customer not found
     */
    public function findByPhoneNumber(string $phoneNumber): Customer
    {
        return $this->customer->whereHas('user', function ($query) use ($phoneNumber): void {
            $query->where('phone_number', $phoneNumber);
        })->firstOrFail();
    }

    /**
     * Find a customer by phone number (alias for findByPhoneNumber).
     *
     * @param string $phoneNumber The phone number to search for
     * @return Customer The customer if found
     * @throws ModelNotFoundException If customer not found
     */
    public function findCustomerByPhoneNumber(string $phoneNumber): Customer
    {
        return $this->findByPhoneNumber($phoneNumber);
    }

    /**
     * Verify a customer's phone number.
     * 
     * @param string $phoneNumber The phone number to verify
     * @return Customer The verified customer
     * @throws Exception If already verified or not found
     */
    public function verifyPhone(string $phoneNumber): Customer
    {
        $customer = $this->findByPhoneNumber($phoneNumber);

        if ($customer->user->verified_at) {
            throw new Exception(
                __('messages.errors.auth.already_verified'),
                Response::HTTP_BAD_REQUEST,
            );
        }

        $customer->user->update([
            'verified_at' => now(),
        ]);

        return $customer->refresh();
    }

    /**
     * Create a password reset token for a customer.
     *
     * @param string $phoneNumber The customer's phone number
     * @return string The generated token
     * @throws ModelNotFoundException If customer not found
     */
    public function createPasswordResetToken(string $phoneNumber): string
    {
        $customer = $this->findByPhoneNumber($phoneNumber);

        return $customer->createToken('password-reset')->plainTextToken;
    }

    /**
     * Update a customer's password.
     *
     * @param string $phoneNumber The customer's phone number
     * @param string $password The new password
     * @throws ModelNotFoundException If customer not found
     */
    public function updatePassword(string $phoneNumber, string $password): void
    {
        $customer = $this->findByPhoneNumber($phoneNumber);

        $customer->user()->update([
            'password' => $password,
        ]);
    }
}
