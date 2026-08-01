<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSongRequest;
use App\Jobs\ProcessPaidSongRequest;
use App\Jobs\SendPaymentConfirmation;
use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Payment\PaymentProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SongRequestController extends Controller
{
    public function __construct(private LyricsGenerator $lyrics) {}

    /**
     * Maak een aanvraag aan en genereer alvast een concept-songtekst.
     */
    public function store(StoreSongRequest $request): JsonResponse
    {
        $intake = $request->input('intake', []);
        $category = $request->input('category');

        // Alleen een snelle lokale concepttekst tijdens de publieke request.
        // De zware AI-rondes draaien na betaling in ProcessPaidSongRequest.
        $generated = $this->lyrics->generateDraft($category, $intake);

        $song = SongRequest::create([
            'category' => $category,
            'category_title' => $request->input('categoryTitle'),
            'email' => $intake['email'] ?? null,
            'intake' => $intake,
            'lyrics' => $generated['lyrics'],
            'lyrics_preview' => $generated['preview'],
            'status' => 'draft',
            'price_cents' => $this->priceCents(),
        ]);

        return response()->json(['data' => $this->present($song)], 201);
    }

    /**
     * Maak een checkout aan. Bij Stripe start uitsluitend de ondertekende
     * webhook de productiepipeline.
     */
    public function checkout(Request $request, SongRequest $songRequest): JsonResponse
    {
        $validated = $request->validate([
            'discount_code' => ['nullable', 'string', 'max:128'],
        ]);

        if ($songRequest->isPaid()) {
            return response()->json(['data' => $this->present($songRequest)]);
        }

        $discountCode = trim((string) ($validated['discount_code'] ?? ''));

        if ($discountCode !== '') {
            return $this->checkoutWithDiscount($songRequest, $discountCode);
        }

        $payment = app(PaymentProvider::class);
        $result = $payment->createCheckout($songRequest);

        $songRequest->forceFill([
            'status' => $result['status'],
            'payment_reference' => $result['reference'],
            'payment_provider' => config('payment.default', 'stub'),
            'paid_at' => $result['status'] === 'paid' ? now() : null,
        ])->save();

        // De stub blijft synchroon, uitsluitend om lokaal en in tests te werken.
        if ($result['status'] === 'paid') {
            SendPaymentConfirmation::dispatchSync($songRequest->id);
            ProcessPaidSongRequest::dispatchSync($songRequest->id);
            $songRequest->refresh();
        }

        return response()->json([
            'data' => $this->present($songRequest) + [
                'checkout_url' => $result['checkout_url'],
            ],
        ]);
    }

    private function checkoutWithDiscount(SongRequest $songRequest, string $discountCode): JsonResponse
    {
        $configuredCode = trim((string) config('payment.discount_code'));

        if ($configuredCode === '' || ! hash_equals($configuredCode, $discountCode)) {
            throw ValidationException::withMessages([
                'discount_code' => 'Deze kortingscode is ongeldig.',
            ]);
        }

        $songRequest = DB::transaction(function () use ($songRequest): SongRequest {
            $lockedSongRequest = SongRequest::whereKey($songRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSongRequest->isPaid()) {
                return $lockedSongRequest;
            }

            $lockedSongRequest->forceFill([
                'status' => 'paid',
                'price_cents' => 0,
                'payment_reference' => 'discount:'.Str::uuid(),
                'payment_provider' => 'discount_code',
                'paid_at' => now(),
            ])->save();

            if (! $lockedSongRequest->payment_fulfillment_queued_at) {
                ProcessPaidSongRequest::dispatch($lockedSongRequest->id)->afterCommit();

                $lockedSongRequest->forceFill([
                    'payment_fulfillment_queued_at' => now(),
                ])->save();
            }

            if (! $lockedSongRequest->payment_confirmation_sent_at) {
                SendPaymentConfirmation::dispatch($lockedSongRequest->id)->afterCommit();
            }

            return $lockedSongRequest->refresh();
        });

        return response()->json([
            'data' => $this->present($songRequest) + [
                'checkout_url' => null,
                'discount_applied' => true,
            ],
        ]);
    }

    public function checkoutStatus(string $sessionId): JsonResponse
    {
        $songRequest = SongRequest::where('payment_provider', 'stripe')
            ->where('payment_reference', $sessionId)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $songRequest->id,
                'category' => $songRequest->category,
                'category_title' => $songRequest->category_title,
                'status' => $songRequest->status,
                'paid' => (bool) $songRequest->paid_at,
            ],
        ]);
    }

    private function priceCents(): int
    {
        // Eén vaste prijs: € 9,99 per nummer.
        return 999;
    }

    private function present(SongRequest $song): array
    {
        return [
            'id' => $song->id,
            'category' => $song->category,
            'status' => $song->status,
            'price_cents' => $song->price_cents,
            'lyrics_preview' => $song->lyrics_preview,
            'production_steps' => $song->production_steps,
            'music_reference' => $song->music_reference,
        ];
    }
}
