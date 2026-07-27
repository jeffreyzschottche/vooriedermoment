<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSongRequest;
use App\Jobs\ProcessPaidSongRequest;
use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Payment\PaymentProvider;
use Illuminate\Http\JsonResponse;

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

        $generated = $this->lyrics->generate($category, $intake);

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
    public function checkout(SongRequest $songRequest, PaymentProvider $payment): JsonResponse
    {
        if ($songRequest->isPaid()) {
            return response()->json(['data' => $this->present($songRequest)]);
        }

        $result = $payment->createCheckout($songRequest);

        $songRequest->forceFill([
            'status' => $result['status'],
            'payment_reference' => $result['reference'],
            'payment_provider' => config('payment.default', 'stub'),
            'paid_at' => $result['status'] === 'paid' ? now() : null,
        ])->save();

        // De stub blijft synchroon, uitsluitend om lokaal en in tests te werken.
        if ($result['status'] === 'paid') {
            ProcessPaidSongRequest::dispatchSync($songRequest->id);
            $songRequest->refresh();
        }

        return response()->json([
            'data' => $this->present($songRequest) + [
                'checkout_url' => $result['checkout_url'],
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
