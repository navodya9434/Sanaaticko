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
                session([
                    'mpesa_pending_' . $checkoutRequestId => $request->except('_token'),
                ]);
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
                $pendingData = session('mpesa_pending_' . $checkoutRequestId);

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
                    session()->forget('mpesa_pending_' . $checkoutRequestId);

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

            if (in_array($code, ['1032', '1037'], true)) {
                session()->forget('mpesa_pending_' . $checkoutRequestId);

                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'message' => $result['ResultDesc']
                        ?? 'M-Pesa payment was not completed.',
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
}
