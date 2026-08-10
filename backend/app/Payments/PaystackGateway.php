<?php

namespace App\Payments;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PaystackGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'paystack';
    }

    public function initialize(Order $order, string $callbackUrl): array
    {
        $payload = [
            'email' => $order->email,
            'amount' => (string) $order->total_amount,
            'currency' => $order->currency,
            'reference' => $order->reference,
            'callback_url' => $callbackUrl,
            'metadata' => json_encode([
                'order_reference' => $order->reference,
                'buyer_name' => $order->name,
                'quantity' => $order->quantity,
                'book_id' => $order->book_id,
                'custom_fields' => [
                    [
                        'display_name' => 'Order Reference',
                        'variable_name' => 'order_reference',
                        'value' => $order->reference,
                    ],
                    [
                        'display_name' => 'Copies',
                        'variable_name' => 'quantity',
                        'value' => (string) $order->quantity,
                    ],
                ],
            ]),
        ];

        $channels = config('payments.paystack.channels', []);

        if ($channels !== []) {
            $payload['channels'] = $channels;
        }

        $response = $this->client()->post('/transaction/initialize', $payload);
        $body = $response->json();
        $data = $body['data'] ?? [];

        if (! $response->successful() || ($body['status'] ?? false) !== true || empty($data['authorization_url'])) {
            throw new RuntimeException($body['message'] ?? 'Paystack could not initialize the transaction.');
        }

        return [
            'authorization_url' => $data['authorization_url'],
            'reference' => $data['reference'] ?? $order->reference,
            'access_code' => $data['access_code'] ?? null,
        ];
    }

    public function verify(string $reference): PaymentResult
    {
        $order = Order::query()
            ->where('reference', $reference)
            ->orWhere('payment_reference', $reference)
            ->first();

        $providerReference = $order?->payment_reference ?: $reference;

        try {
            $response = $this->client()->get('/transaction/verify/'.rawurlencode($providerReference));
        } catch (Throwable $exception) {
            report($exception);

            return PaymentResult::pending(
                $order?->reference ?? $reference,
                'Payment status could not be confirmed yet. Please try again shortly.',
                ['provider' => 'paystack', 'verification_error' => $exception::class],
            );
        }

        $body = $response->json();
        $data = $body['data'] ?? [];
        $internalReference = $order?->reference ?? $reference;
        $meta = ['provider' => 'paystack', 'paystack' => $data, 'provider_response' => $body];

        if ($response->serverError() || $response->status() === 429) {
            return PaymentResult::pending(
                $internalReference,
                'Payment status could not be confirmed yet. Please try again shortly.',
                $meta + ['provider_status' => $response->status()],
            );
        }

        if (! $response->successful() || ($body['status'] ?? false) !== true || $data === []) {
            return PaymentResult::failure(
                $internalReference,
                $body['message'] ?? 'Payment could not be verified.',
                $meta,
            );
        }

        $gatewayStatus = strtolower((string) ($data['status'] ?? ''));

        if ($gatewayStatus !== 'success') {
            return $this->nonSuccessfulResult($internalReference, $gatewayStatus, $meta);
        }

        if ($order !== null) {
            $amountMatches = (int) ($data['amount'] ?? 0) === (int) $order->total_amount;
            $currencyMatches = strtoupper((string) ($data['currency'] ?? '')) === strtoupper($order->currency);

            if (! $amountMatches || ! $currencyMatches) {
                return PaymentResult::failure(
                    $internalReference,
                    'Payment verification failed because the amount or currency did not match the order.',
                    $meta + [
                        'expected_amount' => $order->total_amount,
                        'expected_currency' => $order->currency,
                    ],
                );
            }
        }

        return PaymentResult::success($internalReference, $meta);
    }

    public function hasValidSignature(string $payload, ?string $signature): bool
    {
        if (! is_string($signature) || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $this->secretKey()), $signature);
    }

    public function webhookIpIsAllowed(?string $ip): bool
    {
        if (! (bool) config('payments.paystack.enforce_webhook_ip_whitelist', false)) {
            return true;
        }

        return in_array($ip, config('payments.paystack.webhook_ips', []), true);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('payments.paystack.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->secretKey())
            ->timeout(20);
    }

    private function secretKey(): string
    {
        $secret = (string) config('payments.paystack.secret_key');

        if ($secret === '') {
            throw new RuntimeException('PAYSTACK_SECRET_KEY is not configured.');
        }

        return $secret;
    }

    private function nonSuccessfulResult(string $reference, string $gatewayStatus, array $meta): PaymentResult
    {
        return match ($gatewayStatus) {
            'pending', 'processing', 'ongoing', 'queued' => PaymentResult::pending(
                $reference,
                'Payment is still being processed. Please check again shortly.',
                $meta,
            ),
            default => PaymentResult::failure(
                $reference,
                'Payment was not completed.',
                $meta,
            ),
        };
    }
}
