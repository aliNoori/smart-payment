<?php

namespace SmartPayment\Contracts;

/**
 * Interface PaymentServiceInterface
 *
 * Defines the contract for integrating payment gateways.
 * Implementations should handle order creation and transaction verification.
 */
interface PaymentServiceInterface
{
    /**
     * Create a new payment order and initiate the transaction.
     *
     * @param array $data The payload required to create the order (e.g. amount, callback URL).
     * @return array Response data from the payment gateway (e.g. transaction ID, redirect URL).
     */
    public function createOrderWithTransaction(array $data): array;

    /**
     * Verify the status of a completed transaction.
     *
     * @param array $data The payload required to verify the transaction (e.g. transaction ID, token).
     * @return array Response data indicating success, failure, or additional metadata.
     */
    public function verifyTransaction(array $data): array;
}
