<?php

namespace App\Providers;

use App\Services\Payment\PaymentProvider;
use App\Services\Payment\StubPaymentProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Betaalprovider. Voorlopig de stub; later Mollie/Stripe inpluggen
        // op basis van config('payment.default').
        $this->app->bind(PaymentProvider::class, function () {
            return match (config('payment.default', 'stub')) {
                default => new StubPaymentProvider(),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
