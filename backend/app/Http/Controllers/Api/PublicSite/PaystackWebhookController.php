<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentEvent;
use App\Payments\PaystackGateway;
use App\Support\OrderPaymentRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaystackGateway $gateway,
        OrderPaymentRecorder $payments,
    ): JsonResponse {
        $payload = $request->getContent();

        if (! $gateway->hasValidSignature($payload, $request->header('x-paystack-signature'))) {
            return response()->json(['message' => 'Invalid Paystack signature.'], 401);
        }

        if (! $gateway->webhookIpIsAllowed($request->ip())) {
            return response()->json(['message' => 'Paystack webhook IP is not allowed.'], 403);
        }

        $event = $request->json()->all();
        $eventName = (string) ($event['event'] ?? 'unknown');
        $transaction = $event['data'] ?? [];

        if ($eventName !== 'charge.success') {
            return response()->json(['message' => 'Webhook ignored.']);
        }

        $paymentReference = (string) ($transaction['reference'] ?? '');

        if ($paymentReference === '') {
            Log::warning('Paystack charge.success webhook arrived without a reference.', ['event' => $event]);

            return response()->json(['message' => 'Webhook ignored.']);
        }

        $order = Order::query()
            ->where('reference', $paymentReference)
            ->orWhere('payment_reference', $paymentReference)
            ->first();

        $paymentEvent = PaymentEvent::firstOrCreate(
            [
                'provider' => 'paystack',
                'event_id' => (string) ($transaction['id'] ?? hash('sha256', $payload)),
            ],
            [
                'event' => $eventName,
                'payment_reference' => $paymentReference,
                'order_id' => $order?->id,
                'payload' => $event,
            ],
        );

        if ($paymentEvent->processed_at !== null) {
            return response()->json(['message' => 'Webhook already processed.']);
        }

        if ($order === null) {
            Log::warning('Paystack webhook could not be matched to an order.', [
                'payment_reference' => $paymentReference,
            ]);

            $paymentEvent->update([
                'processing_status' => 'ignored',
                'message' => 'No matching order.',
                'processed_at' => now(),
            ]);

            return response()->json(['message' => 'Webhook accepted.']);
        }

        $result = $gateway->verify($paymentReference);
        $order = $payments->record($order, $result);

        if ($result->orderStatus === Order::STATUS_PENDING) {
            $paymentEvent->update([
                'order_id' => $order->id,
                'processing_status' => 'pending',
                'message' => $result->message,
            ]);

            return response()->json(['message' => 'Payment verification is pending.'], 503);
        }

        $paymentEvent->update([
            'order_id' => $order->id,
            'processing_status' => $result->successful ? 'processed' : 'rejected',
            'message' => $result->message,
            'processed_at' => now(),
        ]);

        return response()->json(['message' => 'Webhook accepted.']);
    }
}
