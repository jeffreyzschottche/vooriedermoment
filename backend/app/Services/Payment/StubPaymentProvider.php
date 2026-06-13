<?php

namespace App\Services\Payment;

use App\Models\SongRequest;
use Illuminate\Support\Str;

/**
 * Tijdelijke betaalprovider: markeert de betaling direct als geslaagd zonder
 * externe call. Vervang later door een Mollie-/Stripe-implementatie van
 * PaymentProvider (en wissel de binding in AppServiceProvider).
 */
class StubPaymentProvider implements PaymentProvider
{
    public function charge(SongRequest $request): array
    {
        return [
            'status' => 'paid',
            'reference' => 'stub_' . Str::lower(Str::random(16)),
        ];
    }
}
