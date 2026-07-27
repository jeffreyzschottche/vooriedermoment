<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Payment\StripeWebhookProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeWebhookProcessor $processor): JsonResponse
    {
        $secret = (string) config('payment.stripe.webhook_secret');

        if ($secret === '') {
            Log::critical('STRIPE_WEBHOOK_SECRET is niet ingesteld.');

            return response()->json(['error' => 'Webhook niet geconfigureerd.'], 500);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $secret,
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Ongeldige Stripe-webhook ontvangen.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Ongeldige webhook.'], 400);
        }

        try {
            $result = $processor->process(
                $event->id,
                $event->type,
                $event->data->object->toArray(),
            );
        } catch (Throwable $exception) {
            Log::error('Stripe-webhook kon niet worden verwerkt.', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Webhookverwerking mislukt.'], 500);
        }

        return response()->json(['received' => true] + $result);
    }
}
