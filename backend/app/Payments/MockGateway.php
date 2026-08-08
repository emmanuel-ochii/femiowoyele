<?php

namespace App\Payments;

use App\Models\Order;

/**
 * Stand-in for a hosted checkout. Instead of redirecting to a provider it sends
 * the buyer to an in-app test screen, and `verify()` reports whatever that
 * screen recorded — letting both the success and failure paths be exercised.
 */
class MockGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'mock';
    }

    public function initialize(Order $order, string $callbackUrl): array
    {
        return [
            // The frontend renders this route as a simulated checkout page.
            'authorization_url' => '/pre-order/checkout?reference='.$order->reference,
            'reference' => $order->reference,
        ];
    }

    public function verify(string $reference): PaymentResult
    {
        $order = Order::where('reference', $reference)->first();

        if ($order === null) {
            return PaymentResult::failure($reference, 'Unknown reference.');
        }

        // The test checkout screen marks the order before verification runs, so
        // a failed simulation stays failed here too.
        if ($order->status === Order::STATUS_FAILED) {
            return PaymentResult::failure($reference, 'Payment was cancelled.');
        }

        return PaymentResult::success($reference, [
            'simulated' => true,
            'channel' => 'mock',
        ]);
    }
}
