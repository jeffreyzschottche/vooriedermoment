<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSongRequest;
use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Payment\PaymentProvider;
use Illuminate\Http\JsonResponse;

class SongRequestController extends Controller
{
    public function __construct(private LyricsGenerator $lyrics)
    {
    }

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
     * Reken (gestubd) af. Daarna STOPT het proces bewust: het daadwerkelijk
     * produceren van het nummer (bv. via Suno) is nog niet ingebouwd.
     */
    public function checkout(SongRequest $songRequest, PaymentProvider $payment): JsonResponse
    {
        if ($songRequest->status !== 'paid') {
            $result = $payment->charge($songRequest);
            $songRequest->update([
                'status' => $result['status'],
                'payment_reference' => $result['reference'],
            ]);

            // TODO: trigger song-generatie (Suno) zodra dat is gekozen/ingebouwd.
            // Voor nu stopt de flow hier na een succesvolle (stub-)betaling.
        }

        return response()->json(['data' => $this->present($songRequest)]);
    }

    private function priceCents(): int
    {
        $saleOn = in_array(env('saleOn', env('SALE_ON')), ['1', 1, true, 'true'], true);

        return $saleOn ? 999 : 1499;
    }

    private function present(SongRequest $song): array
    {
        return [
            'id' => $song->id,
            'category' => $song->category,
            'status' => $song->status,
            'price_cents' => $song->price_cents,
            'lyrics_preview' => $song->lyrics_preview,
        ];
    }
}
