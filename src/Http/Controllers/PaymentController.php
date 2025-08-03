<?php

namespace SmartPayment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use SmartPayment\Contracts\PaymentServiceInterface;
use Exception;

/**
 * Class PaymentController
 *
 * Handles payment initiation and callback verification.
 * Delegates logic to the PaymentServiceInterface for gateway-agnostic operations.
 *
 * @package SmartPayment\Http\Controllers
 */
class PaymentController extends Controller
{
    /**
     * Injects the payment service via constructor.
     *
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(protected PaymentServiceInterface $paymentService) {}

    /**
     * Initiates a payment request using the selected gateway.
     *
     * @param Request $request Incoming HTTP request containing payment data
     * @return JsonResponse Contains order ID and redirect URL
     *
     * @throws Exception If gateway fails or validation fails
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'gateway' => 'nullable|string',
            'callback_url' => 'nullable|url',
            'meta' => 'nullable|array',
        ]);

        try {

            $result = $this->paymentService->createOrderWithTransaction($data);

            return response()->json([
                'status' => 'pending',
                'order_id' => $result['order']->id,
                'redirect_url' => $result['redirect_url'],
            ]);
        } catch (Exception $e) {
            Log::error('Payment initiation error', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handles the callback from the payment gateway and verifies the transaction.
     *
     * @param Request $request Incoming callback request from gateway
     * @return JsonResponse Contains order ID and reference ID if successful
     *
     * @throws Exception If verification fails or transaction is invalid
     */
    public function callback(Request $request): JsonResponse
    {
        try {

            $result = $this->paymentService->verifyTransaction($request->all());

            return response()->json([
                'status' => 'success',
                'order_id' => $result['order']->id,
                'ref_id' => $result['ref_id'],
            ]);
        } catch (Exception $e) {
            Log::error('Payment verification error', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
