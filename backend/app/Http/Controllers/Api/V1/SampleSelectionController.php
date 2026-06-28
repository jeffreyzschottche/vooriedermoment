<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SongRequest;
use Illuminate\Http\Request;

class SampleSelectionController extends Controller
{
    /**
     * Get samples for selection (public, uses token)
     */
    public function getSamples(string $token)
    {
        $songRequest = SongRequest::where('selection_token', $token)->first();

        if (!$songRequest) {
            return response()->json(['error' => 'Aanvraag niet gevonden'], 404);
        }

        if (!$songRequest->hasSamples()) {
            return response()->json(['error' => 'Samples zijn nog niet klaar'], 400);
        }

        if ($songRequest->chosen_sample_id) {
            return response()->json([
                'already_chosen' => true,
                'chosen_sample_id' => $songRequest->chosen_sample_id,
                'message' => 'Je hebt al een keuze gemaakt',
            ]);
        }

        return response()->json([
            'recipient_name' => $songRequest->recipient_name,
            'category_title' => $songRequest->category_title,
            'samples' => $songRequest->samples,
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

        $songRequest = SongRequest::where('selection_token', $token)->first();

        if (!$songRequest) {
            return response()->json(['error' => 'Aanvraag niet gevonden'], 404);
        }

        if (!$songRequest->hasSamples()) {
            return response()->json(['error' => 'Samples zijn nog niet klaar'], 400);
        }

        if ($songRequest->chosen_sample_id) {
            return response()->json([
                'error' => 'Je hebt al een keuze gemaakt',
                'chosen_sample_id' => $songRequest->chosen_sample_id,
            ], 400);
        }

        // Validate sample exists
        $sampleIds = array_column($songRequest->samples, 'id');
        if (!in_array($request->sample_id, $sampleIds)) {
            return response()->json(['error' => 'Ongeldige sample'], 400);
        }

        $songRequest->update([
            'chosen_sample_id' => $request->sample_id,
            'status' => 'sample_chosen',
        ]);

        // TODO: Notify admin that sample was chosen

        return response()->json([
            'message' => 'Bedankt voor je keuze! We gaan aan de slag met de volledige versie.',
            'chosen_sample_id' => $request->sample_id,
        ]);
    }
}
