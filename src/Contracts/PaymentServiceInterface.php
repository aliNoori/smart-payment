<?php

namespace SmartPayment\Contracts;

interface PaymentServiceInterface
{
    public function createOrderWithTransaction(array $data): array;

    public function verifyTransaction(array $data): array;
}
