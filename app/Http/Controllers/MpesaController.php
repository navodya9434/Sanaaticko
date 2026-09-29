<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function initiate(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'mpesa_phone' => ['required', 'regex:/^2547\d{8}$/'],
            'mpesa_amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $result = $mpesa->stkPush(
                $request->mpesa_phone,
                (float) $request->mpesa_amount,
                'SANAATIKO',
                'Ticket payment'
            );

            $checkoutRequestId = $result['CheckoutRequestID'] ?? null;

            if ($checkoutRequestId) {
                cache()->put(
                    'mpesa_pending_' . $checkoutRequestId,
                    $request->except('_token'),
                    now()->addMinutes(30)
                );
            }

            return response()->json([
                'success' => (($result['ResponseCode'] ?? '') === '0'),
                'message' => $result['CustomerMessage']
                    ?? $result['ResponseDescription']
                    ?? 'M-Pesa request sent.',
                'checkout_request_id' => $checkoutRequestId,
            ]);
        } catch (\Throwable $e) {
            Log::error('M-Pesa initiate error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function query(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'checkout_request_id' => ['required', 'string'],
        ]);

        $checkoutRequestId = $request->checkout_request_id;

        try {
            $result = $mpesa->stkQuery($checkoutRequestId);
            $code = (string) ($result['ResultCode'] ?? '');

            if ($code === '0') {
                $pendingData = cache()->get('mpesa_pending_' . $checkoutRequestId);

                if (!$pendingData) {
                    return response()->json([
                        'success' => false,
                        'status' => 'failed',
                        'message' => 'Payment was confirmed, but the pending booking data was not found.',
                    ], 500);
                }

                $pendingData['payment_type'] = 'MPESA';
                $pendingData['payment'] = $pendingData['mpesa_amount'] ?? 0;
                $pendingData['mpesa_payment_status'] = 'paid';

                $orderRequest = Request::create(
                    '/createOrder',
                    'POST',
                    $pendingData
                );

                $orderResponse = app(FrontendController::class)
                    ->createOrder($orderRequest);

                $orderData = json_decode(
                    $orderResponse->getContent(),
                    true
                );

                if (
                    $orderResponse->getStatusCode() >= 200 &&
                    $orderResponse->getStatusCode() < 300 &&
                    ($orderData['success'] ?? false)
                ) {
                    cache()->forget('mpesa_pending_' . $checkoutRequestId);

                    return response()->json([
                        'success' => true,
                        'status' => 'paid',
                        'message' => 'Payment confirmed and order created.',
                        'order_id' => $orderData['order_id'] ?? null,
                    ]);
                }

                Log::error('M-Pesa payment confirmed but order creation failed', [
                    'checkout_request_id' => $checkoutRequestId,
                    'order_response' => $orderData,
                ]);

                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'message' => $orderData['message']
                        ?? 'Payment confirmed but order creation failed.',
                ], 500);
            }

            if ($code === '1032') {
                cache()->forget('mpesa_pending_' . $checkoutRequestId);

                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'result_code' => $code,
                    'message' => $result['ResultDesc']
                        ?? 'M-Pesa payment was cancelled.',
                ]);
            }

            if ($code === '1037') {
                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'result_code' => $code,
                    'message' => $result['ResultDesc']
                        ?? 'Waiting for M-Pesa confirmation.',
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => $result['ResultDesc']
                    ?? 'Payment is still being processed.',
            ]);
        } catch (\Throwable $e) {
            Log::error('M-Pesa query error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function callback(Request $request)
    {
        Log::info('M-Pesa callback received', [
            'payload' => $request->all(),
        ]);

        $callback = $request->input('Body.stkCallback', []);
        $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
        $resultCode = (string) ($callback['ResultCode'] ?? '');

        if ($checkoutRequestId && $resultCode === '0') {
            $pendingData = cache()->get('mpesa_pending_' . $checkoutRequestId);

            if ($pendingData) {
                $pendingData['payment_type'] = 'MPESA';
                $pendingData['payment'] = $pendingData['mpesa_amount'] ?? 0;
                $pendingData['mpesa_payment_status'] = 'paid';

                try {
                    $orderRequest = Request::create(
                        '/createOrder',
                        'POST',
                        $pendingData
                    );

                    $orderResponse = app(FrontendController::class)
                        ->createOrder($orderRequest);

                    $orderData = json_decode(
                        $orderResponse->getContent(),
                        true
                    );

                    if (
                        $orderResponse->getStatusCode() >= 200 &&
                        $orderResponse->getStatusCode() < 300 &&
                        ($orderData['success'] ?? false)
                    ) {
                        cache()->put(
                            'mpesa_result_' . $checkoutRequestId,
                            [
                                'success' => true,
                                'status' => 'paid',
                                'order_id' => $orderData['order_id'] ?? null,
                            ],
                            now()->addMinutes(30)
                        );

                        cache()->forget(
                            'mpesa_pending_' . $checkoutRequestId
                        );
                    }
                } catch (\Throwable $e) {
                    Log::error('M-Pesa callback order error', [
                        'checkout_request_id' => $checkoutRequestId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

}







