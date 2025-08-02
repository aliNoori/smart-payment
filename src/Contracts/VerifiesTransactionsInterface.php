<?php

namespace SmartPayment\Contracts;

interface VerifiesTransactionsInterface
{
    /**
     * Verify a payment after callback from gateway.
     */
    public function verify(array $requestData): array;
}
