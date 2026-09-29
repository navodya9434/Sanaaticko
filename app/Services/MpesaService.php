<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken(): string
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret')
        )->get($this->baseUrl . '/oauth/v1/generate', [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            throw new \Exception('M-Pesa OAuth failed: ' . $response->body());
        }

        return $response->json('access_token');
    }

    public function stkPush(
        string $phone,
        float $amount,
        string $accountReference = 'SANAATIKO',
        string $description = 'Ticket payment'
    ): array {
        $timestamp = now()->format('YmdHis');

        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');

        $password = base64_encode(
            $shortcode . $passkey . $timestamp
        );

        $response = Http::withToken($this->getAccessToken())
            ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => (int) $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $amount,
                'PartyA' => $phone,
                'PartyB' => (int) $shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => config('services.mpesa.callback_url'),
                'AccountReference' => substr($accountReference, 0, 12),
                'TransactionDesc' => substr($description, 0, 13),
            ]);

        if ($response->failed()) {
            throw new \Exception(
                'M-Pesa STK Push failed: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function stkQuery(string $checkoutRequestId): array
{
    $timestamp = now()->format('YmdHis');

    $shortcode = config('services.mpesa.shortcode');
    $passkey = config('services.mpesa.passkey');

    $password = base64_encode(
        $shortcode . $passkey . $timestamp
    );

    $response = Http::withToken($this->getAccessToken())
        ->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', [
            'BusinessShortCode' => (int) $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ]);

    if ($response->failed()) {
        throw new \Exception(
            'M-Pesa STK Query failed: ' . $response->body()
        );
    }

    return $response->json();
}
}