<?php

namespace App\Payments;

use App\Models\Order;

/**
 * Contract every payment provider implements.
 *
 * Modelled on Paystack's own two-step flow — initialise, then verify — so
 * swapping the mock for the real provider is a driver change rather than a
 * rewrite of the pre-order journey.
 */
interface PaymentGateway
{
    /** Provider name recorded against the order. */
    public function name(): string;

    /**
     * Begins a payment and returns where to send the buyer.
     *
     * @return array{authorization_url: string, reference: string}
     */
    public function initialize(Order $order, string $callbackUrl): array;

    /** Confirms the true status of a payment with the provider. */
    public function verify(string $reference): PaymentResult;
}
