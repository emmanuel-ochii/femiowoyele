<?php

namespace App\Payments;

/** Outcome of verifying a payment with the provider. */
class PaymentResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly string $reference,
        public readonly array $meta = [],
        public readonly ?string $message = null,
        public readonly ?string $orderStatus = null,
    ) {}

    public static function success(string $reference, array $meta = []): self
    {
        return new self(true, $reference, $meta, null, 'paid');
    }

    public static function failure(string $reference, ?string $message = null, array $meta = []): self
    {
        return new self(false, $reference, $meta, $message, 'failed');
    }

    public static function pending(string $reference, ?string $message = null, array $meta = []): self
    {
        return new self(false, $reference, $meta, $message, 'pending');
    }
}
