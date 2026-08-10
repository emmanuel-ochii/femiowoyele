<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Driver
    |--------------------------------------------------------------------------
    |
    | `mock` completes payments locally without contacting a provider, so the
    | whole pre-order flow can be exercised end to end before Paystack is live.
    | Switching to `paystack` should require only this value and the keys below.
    |
    */

    'driver' => env('PAYMENT_DRIVER', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | Currency & Pricing
    |--------------------------------------------------------------------------
    |
    | Amounts are held in minor units (kobo). Paystack charges in kobo, and
    | integers keep totals free of floating-point rounding.
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'NGN'),
    'currency_symbol' => env('PAYMENT_CURRENCY_SYMBOL', '₦'),
    'book_price_minor' => (int) env('BOOK_PRICE_MINOR', 999990),

    'max_quantity' => 20,

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'channels' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('PAYSTACK_CHANNELS', '')),
        ))),
        'webhook_ips' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('PAYSTACK_WEBHOOK_IPS', '52.31.139.75,52.49.173.169,52.214.14.220')),
        ))),
        // Signature verification is always enforced. IP whitelisting is useful
        // in production, but defaults off so local tunnels and proxies still work.
        'enforce_webhook_ip_whitelist' => filter_var(
            env('PAYSTACK_ENFORCE_WEBHOOK_IP_WHITELIST', false),
            FILTER_VALIDATE_BOOL,
        ),
    ],

    /*
    | Where the provider returns the buyer once payment is attempted. The
    | reference is appended as a query parameter.
    */
    'callback_path' => '/pre-order/complete',

];
