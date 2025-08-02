<?php

namespace SmartPayment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Prepare a payment and return redirect URL or token.
     */
    public function initiatePayment(float $amount, string $callbackUrl, array $meta = []): mixed;

    /**
     * Get URL user should be redirected to (optional).
     */
    public function getRedirectUrl(): string;

    /**
     * Check if gateway uses redirect (vs form-based).
     */
    public function shouldRedirect(): bool;
}
