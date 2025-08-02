<?php

namespace SmartPayment\Gateways;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SmartPayment\Contracts\PaymentGatewayInterface;
use SmartPayment\Contracts\VerifiesTransactionsInterface;
use Exception;

class ZarinpalGateway implements PaymentGatewayInterface, VerifiesTransactionsInterface
{
    protected string $merchantId;
    protected bool $sandbox;
    protected string $callbackUrl;
    protected string $redirectUrl;
    protected string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('smart-payment.gateways.zarinpal.merchant_id');
        $this->sandbox = config('smart-payment.gateways.zarinpal.sandbox', false);
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/request.json';

    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function initiatePayment(float $amount, string $callbackUrl, array $meta = []): mixed
    {
        $this->callbackUrl = $callbackUrl;

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl, [
            'merchant_id' => $this->merchantId,
            'amount' => (int) $amount,
            'callback_url' => $this->callbackUrl,
            'description' => $meta['description'] ?? 'پرداخت سفارش',
            'metadata' => [
                'email' => $meta['email'] ?? null,
                'mobile' => $meta['mobile'] ?? null,
            ],
        ]);


        $data = $response->json();

        Log::info('Zarinpal PaymentRequest', ['request' => $response->body()]);

        if ($data['data']['code'] != 100 || empty($data['data']['authority'])) {
            throw new Exception('Zarinpal Payment Error: ' . $data['data']['code']);
        }

        $authority = $data['data']['authority'];
        $this->redirectUrl = $this->sandbox
            ? "https://sandbox.zarinpal.com/pg/StartPay/{$authority}"
            : "https://payment.zarinpal.com/pg/StartPay/{$authority}";

        return [
            'authority' => $authority,
            'redirect_url' => $this->redirectUrl,
        ];
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function verify(array $requestData): array
    {

        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/verify.json';


        $authority = $requestData['Authority'] ?? null;
        $status = $requestData['Status'] ?? null;

        if (!$authority || strtolower($status) !== 'ok') {
            throw new Exception('پرداخت توسط کاربر لغو شد یا داده ناقص بود.');
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->baseUrl, [
            'merchant_id' => $this->merchantId,
            'authority' => $authority,
            'amount' => (int)$requestData['Amount'], // در پروژه واقعی، مقدار باید از DB خونده شه
        ]);

        $data = $response->json();

        Log::info('Zarinpal PaymentVerification', ['response' => $data]);

        if (isset($data['data']['code'])) {
            if ($data['data']['code'] == 100) {
                return [
                    'Message' => $data['data']['message'],
                    'RefID' => $data['data']['ref_id'],
                    'CardHash' => $data['data']['card_hash'],
                    'CardPan' => $data['data']['card_pan'],
                    'Fee' => $data['data']['fee'],
                    'FeeType' => $data['data']['fee_type'],
                    'ShaparakFee'=>$data['data']['shaparak_fee'],
                    'OrderId'=>$data['data']['order_id'],
                ];
            } elseif ($data['data']['code'] == 101) {
                return [
                    'message' => 'Payment already verified',
                    'ref_id' => $data['data']['ref_id'],
                ];
            }
        }

        return ['error' => 'Payment failed or unauthorized transaction'];
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function shouldRedirect(): bool
    {
        return true;
    }
}
