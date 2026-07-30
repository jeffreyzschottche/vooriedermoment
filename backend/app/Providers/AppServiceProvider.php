<?php

namespace App\Providers;

use App\Services\Music\MusicProvider;
use App\Services\Music\StubMusicProvider;
use App\Services\Payment\PaymentProvider;
use App\Services\Payment\StripePaymentProvider;
use App\Services\Payment\StubPaymentProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentProvider::class, function () {
            return match (config('payment.default', 'stub')) {
                'stripe' => new StripePaymentProvider(new StripeClient(
                    config('payment.stripe.secret_key')
                        ?: throw new RuntimeException('STRIPE_SECRET_KEY is niet ingesteld.')
                )),
                default => new StubPaymentProvider,
            };
        });

        $this->app->bind(MusicProvider::class, function ($app) {
            return match (config('music.default', 'stub')) {
                default => $app->make(StubMusicProvider::class),
            };
        });
    }

    public function boot(): void
    {
        if ($this->app->isProduction() || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
