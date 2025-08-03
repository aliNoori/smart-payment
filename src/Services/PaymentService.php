<?php

namespace SmartPayment\Services;

use SmartPayment\Contracts\PaymentServiceInterface;
use SmartPayment\Core\PaymentManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

/**
 * Class PaymentService
 *
 * Handles payment flow logic including order creation, transaction initiation,
 * and verification. Uses dynamic model resolution from config for flexibility.
 *
 * @package SmartPayment\Services
 */
class PaymentService implements PaymentServiceInterface
{
    /**
     * Create a new order and initiate a payment transaction.
     *
     * @param array $data Input data including amount, gateway, callback_url, and meta
     * @return array Contains order instance and redirect URL
     *
     * @throws Exception
     */
    public function createOrderWithTransaction(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $userId = auth()->id();
            $gatewayName = $data['gateway'] ?? config('smart-payment.default');
            $callbackUrl = $data['callback_url'] ?? route('payment.callback');

            // Dynamically resolve Order model from config
            $orderModel = config('smart-payment.models.order');
            $order = $orderModel::create([
                'user_id' => $userId,
                'amount' => $data['amount'],
                'status' => 'pending',
                'description' => $data['meta']['description'] ?? null,
            ]);

            // Resolve gateway instance
            $gateway = PaymentManager::resolve($gatewayName);

            // Initiate payment and get redirect URL
            $result = $gateway->initiatePayment(
                $data['amount'],
                $callbackUrl . '?gateway=' . $gatewayName . '&order_id=' . $order->id,
                $data['meta'] ?? []
            );

            // Dynamically resolve Transaction model from config
            $transactionModel = config('smart-payment.models.transaction');
            $transactionModel::create([
                'order_id' => $order->id,
                'gateway' => $gatewayName,
                'amount' => $data['amount'],
                'authority' => $result['authority'] ?? null,
                'status' => 'pending',
            ]);

            return [
                'status' => 'pending',
                'order' => $order,
                'redirect_url' => $result['redirect_url'] ?? null,
            ];
        });
    }

    /**
     * Verify a transaction after user returns from the gateway.
     *
     * @param array $data Callback data from the gateway
     * @return array Contains verified order and reference ID
     *
     * @throws Exception
     */
    public function verifyTransaction(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $orderId = $data['order_id'];
            $gatewayName = $data['gateway'] ?? config('smart-payment.default');
            $authority = $data['Authority'] ?? null;

            // Dynamically resolve Order model from config
            $orderModel = config('smart-payment.models.order');
            $order = $orderModel::findOrFail($orderId);

            $gateway = PaymentManager::resolve($gatewayName);

            // Verify transaction with gateway
            $verifyResult = $gateway->verify([
                ...$data,
                'Amount' => $order->amount,
            ]);

            // Dynamically resolve Transaction model from config
            $transactionModel = config('smart-payment.models.transaction');
            $transaction = $transactionModel::where('order_id', $order->id)
                ->where('authority', $authority)
                ->firstOrFail();

            // Update transaction with verification result
            $transaction->update([
                'status' => $verifyResult['Message'] ?? null,
                'ref_id' => $verifyResult['RefID'] ?? null,
                'card_pan' => $verifyResult['CardPan'] ?? null,
                'card_hash' => $verifyResult['CardHash'] ?? null,
                'paid_at' => Carbon::now(),
            ]);

            // Update order status
            $order->update(['status' => 'paid']);

            return [
                'order' => $order,
                'ref_id' => $verifyResult['RefID'] ?? null,
            ];
        });
    }
}
