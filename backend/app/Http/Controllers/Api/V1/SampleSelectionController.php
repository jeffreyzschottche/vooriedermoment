<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteOrderSamples;
use App\Mail\SampleChosenMail;
use App\Models\SongSample;
use App\Models\SongRequest;
use App\Services\Orders\SamplePayloadFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SampleSelectionController extends Controller
{
    public function __construct(private SamplePayloadFactory $payloads) {}

    /**
     * Get samples for selection (public, uses token)
     */
    public function getSamples(string $token)
    {
        $songRequest = SongRequest::with('songSamples')
            ->where('selection_token', $token)
            ->first();

        if (! $songRequest) {
            return response()->json(['error' => 'Aanvraag niet gevonden'], 404);
        }

        if ($songRequest->chosen_sample_id) {
            return response()->json([
                'already_chosen' => true,
                'chosen_sample_id' => $songRequest->chosen_sample_id,
                'chosen_sample_title' => $songRequest->chosen_sample_title,
                'message' => 'Je hebt al een keuze gemaakt',
            ]);
        }

        if (! $songRequest->hasSamples()) {
            return response()->json(['error' => 'Samples zijn nog niet klaar'], 400);
        }

        return response()->json([
            'recipient_name' => $songRequest->recipient_name,
            'category_title' => $songRequest->category_title,
            'samples' => $songRequest->songSamples->isNotEmpty()
                ? $this->payloads->forSelection($songRequest)
                : $songRequest->samples,
            'created_at' => $songRequest->samples_generated_at?->toISOString(),
        ]);
    }

    /**
     * Submit sample choice (public, uses token)
     */
    public function chooseSample(Request $request, string $token)
    {
        $request->validate([
            'sample_id' => 'required|integer|min:1|max:4',
        ]);

        $songRequest = SongRequest::with('songSamples')
            ->where('selection_token', $token)
            ->first();

        if (! $songRequest) {
            return response()->json(['error' => 'Aanvraag niet gevonden'], 404);
        }

        if (! $songRequest->hasSamples()) {
            return response()->json(['error' => 'Samples zijn nog niet klaar'], 400);
        }

        if ($songRequest->chosen_sample_id) {
            return response()->json([
                'error' => 'Je hebt al een keuze gemaakt',
                'chosen_sample_id' => $songRequest->chosen_sample_id,
            ], 400);
        }

        $sampleIds = $songRequest->songSamples->isNotEmpty()
            ? $songRequest->songSamples->pluck('position')->all()
            : array_column($songRequest->samples, 'id');

        if (! in_array($request->sample_id, $sampleIds, true)) {
            return response()->json(['error' => 'Ongeldige sample'], 400);
        }

        $chosenSample = DB::transaction(function () use ($songRequest, $request): SongSample {
            $lockedOrder = SongRequest::whereKey($songRequest->id)->lockForUpdate()->firstOrFail();

            abort_if($lockedOrder->chosen_sample_id, 409, 'Er is al een sample gekozen.');

            $chosenSample = $lockedOrder->songSamples()
                ->where('position', $request->integer('sample_id'))
                ->firstOrFail();

            $lockedOrder->forceFill([
                'chosen_sample_id' => (string) $request->integer('sample_id'),
                'chosen_sample_position' => $chosenSample->position,
                'chosen_sample_title' => $chosenSample->title,
                'chosen_suno_source_url' => $chosenSample->suno_source_url,
                'status' => 'sample_chosen',
            ])->save();

            DeleteOrderSamples::dispatch($lockedOrder->id)->afterCommit();

            return $chosenSample;
        });

        $notifyEmail = config('orders.notify_email');

        if (filled($notifyEmail)) {
            Mail::to($notifyEmail)->send(new SampleChosenMail($songRequest->refresh(), $chosenSample));
        }

        return response()->json([
            'message' => 'Bedankt voor je keuze! De tijdelijke previews worden nu verwijderd.',
            'chosen_sample_id' => $request->sample_id,
        ]);
    }
}
