<?php

namespace App\Services\Payment;

use App\Models\SongRequest;

interface PaymentProvider
{
    /**
     * Verwerk de betaling voor een aanvraag.
     *
     * @return array{status: string, reference: string}
     */
    public function charge(SongRequest $request): array;
}
