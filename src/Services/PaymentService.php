<?php

namespace SmartPayment\Services;

use SmartPayment\Contracts\PaymentServiceInterface;
use SmartPayment\Factories\GatewayFactory;
use SmartPayment\Models\Order;
use SmartPayment\Models\Transaction;
use SmartPayment\Core\PaymentManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentService implements PaymentServiceInterface
{
    public function createOrderWithTransaction(array $data): array
    {

        return DB::transaction(function () use ($data) {
            $userId = auth()->id();
            $gatewayName = $data['gateway'] ?? config('smart-payment.default');
            $callbackUrl = $data['callback_url'] ?? route('payment.callback');

            // ایجاد سفارش
            $order = Order::create([
                'user_id' => $userId,
                'amount' => $data['amount'],
                'status' => 'pending',
                'description' => $data['meta']['description'] ?? null,
            ]);

            // ایجاد درگاه
            $gateway = PaymentManager::resolve($gatewayName);

            // ایجاد لینک پرداخت
            $result = $gateway->initiatePayment(
                $data['amount'],
                $callbackUrl . '?gateway=' . $gatewayName . '&order_id=' . $order->id,
                $data['meta'] ?? []
            );

            // ثبت تراکنش اولیه
            Transaction::create([
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

    public function verifyTransaction(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $orderId = $data['order_id'];
            $gatewayName = $data['gateway'] ?? config('smart-payment.default');
            $authority = $data['Authority'] ?? null;

            $order = Order::findOrFail($orderId);
            $gateway = PaymentManager::resolve($gatewayName);

            $verifyResult = $gateway->verify([
                ...$data,
                'Amount' => $order->amount,
            ]);

            $transaction = $order->transactions()
                ->where('authority', $authority)
                ->firstOrFail();

            $transaction->update([
                'status' => $verifyResult['Message'] ?? null,
                'ref_id' => $verifyResult['RefID'] ?? null,
                'card_pan' => $verifyResult['CardPan'] ?? null,
                'card_hash' => $verifyResult['CardHash'] ?? null,
                'paid_at' => Carbon::now(),
            ]);

            $order->update(['status' => 'paid']);

            return [
                'order' => $order,
                'ref_id' => $verifyResult['RefID'] ?? null,
            ];
        });
    }
}
