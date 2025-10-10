<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Exception;
use App\Models\Customer;

/**
 * Interface CustomerRepositoryInterface.
 *
 * Defines the contract for customer repository operations.
 */
interface CustomerRepositoryInterface
{
    /**
     * Create a new customer.
     *
     * @param  array<string, mixed> $createCustomerData The data for creating a new customer
     * @return Customer             The created customer
     */
    public function createCustomer(array $createCustomerData): Customer;

    /**
     * Get a customer by user ID.
     *
     * @param  int           $userId The user ID
     * @return Customer|null The customer if found, null otherwise
     */
    public function getCustomerByUserId(int $userId): ?Customer;

    /**
     * Update a customer by ID.
     *
     * @param  int                  $customerId         The ID of the customer to update
     * @param  array<string, mixed> $updateCustomerData The data to update the customer with
     * @return Customer             The updated customer
     *
     * @throws Exception When customer is not found (HTTP 404)
     */
    public function updateCustomerById(int $customerId, array $updateCustomerData): Customer;

    /**
     * Find a customer by their phone number.
     *
     * @param  string        $phoneNumber The phone number to search for
     * @return Customer|null The matching customer or null if not found
     */
    public function findByPhoneNumber(string $phoneNumber): ?Customer;

    /**
     * Verify a customer's phone number.
     *
     * @param  string   $phoneNumber The phone number to verify
     * @return Customer The verified customer
     *
     * @throws Exception When customer is not found (HTTP 404)
     */
    public function verifyPhone(string $phoneNumber): Customer;

    /**
     * Create a password reset token for a customer.
     *
     * @param  string $phoneNumber The customer's phone number
     * @return string The generated password reset token
     *
     * @throws Exception When customer is not found (HTTP 404)
     */
    public function createPasswordResetToken(string $phoneNumber): string;

    /**
     * Update a customer's password.
     *
     * @param string $phoneNumber The customer's phone number
     * @param string $password    The new password
     *
     * @throws Exception When customer is not found (HTTP 404)
     */
    public function updatePassword(string $phoneNumber, string $password): void;
}
