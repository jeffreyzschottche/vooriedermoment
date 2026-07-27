<?php

namespace App\Services\Payment;

use App\Models\SongRequest;

interface PaymentProvider
{
    /**
     * Maak een betaalcheckout voor een aanvraag.
     *
     * @return array{status: string, reference: string, checkout_url: ?string}
     */
    public function createCheckout(SongRequest $request): array;
}
