<?php

namespace SmartPayment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use SmartPayment\Contracts\PaymentServiceInterface;
use Exception;


class PaymentController extends Controller
{
    public function __construct(protected PaymentServiceInterface $paymentService) {}

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
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

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
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
    }
}
