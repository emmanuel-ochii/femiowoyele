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
    ],

    /*
    | Where the provider returns the buyer once payment is attempted. The
    | reference is appended as a query parameter.
    */
    'callback_path' => '/pre-order/complete',

];
