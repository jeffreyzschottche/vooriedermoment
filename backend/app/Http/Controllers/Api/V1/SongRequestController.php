<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSongRequest;
use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Orders\OrderExporter;
use App\Services\Payment\PaymentProvider;
use App\Services\Production\SongProductionPipeline;
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
     * Reken af en start daarna de productiepipeline.
     */
    public function checkout(SongRequest $songRequest, PaymentProvider $payment, SongProductionPipeline $production, OrderExporter $orders): JsonResponse
    {
        if ($songRequest->status !== 'paid') {
            $result = $payment->charge($songRequest);
            $songRequest->update([
                'status' => $result['status'],
                'payment_reference' => $result['reference'],
            ]);
        }

        if (in_array($songRequest->status, ['paid', 'production_failed'], true)) {
            $songRequest = $production->run($songRequest->refresh());
        }

        // Betaalde aanvraag wegschrijven als Suno-klare JSON (lokaal) + notificatie.
        if ($songRequest->isPaid()) {
            $orders->export($songRequest);
        }

        return response()->json(['data' => $this->present($songRequest)]);
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
