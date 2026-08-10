<?php

namespace App\Providers;

use App\Models\User;
use App\Payments\MockGateway;
use App\Payments\PaymentGateway;
use App\Payments\PaystackGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Resolved from config so local mock payments and production Paystack
        // payments use the same pre-order flow.
        $this->app->bind(PaymentGateway::class, function () {
            return match (config('payments.driver')) {
                'paystack' => new PaystackGateway,
                default => new MockGateway,
            };
        });

        //
    }

    public function boot(): void
    {
        Gate::define('manage-content', function (User $user): bool {
            return in_array($user->role, ['admin', 'editor'], true);
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perMinute(12)->by($request->ip());
        });

        RateLimiter::for('pre-order', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('rsvp', function (Request $request) {
            return Limit::perMinutes(10, 8)->by($request->ip());
        });
    }
}
