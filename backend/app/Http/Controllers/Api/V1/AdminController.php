<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SongRequest;
use App\Mail\SamplesReadyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Get all pending song requests (paid but no samples yet)
     */
    public function pendingRequests()
    {
        $requests = SongRequest::where('status', 'paid')
            ->whereNull('samples')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => $this->formatRequest($r));

        return response()->json(['data' => $requests]);
    }

    /**
     * Get all song requests with their status
     */
    public function allRequests(Request $request)
    {
        $query = SongRequest::orderBy('created_at', 'desc');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $requests = $query->get()->map(fn($r) => $this->formatRequest($r));

        return response()->json(['data' => $requests]);
    }

    /**
     * Get a single song request with full details
     */
    public function showRequest(SongRequest $songRequest)
    {
        return response()->json([
            'data' => $this->formatRequest($songRequest, full: true)
        ]);
    }

    /**
     * Upload samples for a song request (4 audio files)
     */
    public function uploadSamples(Request $request, SongRequest $songRequest)
    {
        $request->validate([
            'samples' => 'required|array|min:1|max:4',
            'samples.*' => 'required|file|mimes:mp3,wav,m4a,ogg|max:20480', // 20MB max per file
        ]);

        $samples = [];

        foreach ($request->file('samples') as $index => $file) {
            $filename = sprintf(
                'samples/%d/%s_%d.%s',
                $songRequest->id,
                Str::slug($songRequest->recipient_name),
                $index + 1,
                $file->getClientOriginalExtension()
            );

            $path = $file->storeAs('public', $filename);
            $url = Storage::url($path);

            $samples[] = [
                'id' => $index + 1,
                'url' => $url,
                'filename' => $file->getClientOriginalName(),
                'duration' => 15, // Assumed 15s samples
                'created_at' => now()->toISOString(),
            ];
        }

        $songRequest->update([
            'samples' => $samples,
            'samples_generated_at' => now(),
            'status' => 'samples_ready',
        ]);

        // Send email to customer
        if ($songRequest->email) {
            Mail::to($songRequest->email)->send(new SamplesReadyMail($songRequest, $samples));
        }

        return response()->json([
            'message' => 'Samples uploaded and email sent',
            'data' => $this->formatRequest($songRequest, full: true),
        ]);
    }

    /**
     * Add sample URLs manually (if hosting externally)
     */
    public function addSampleUrls(Request $request, SongRequest $songRequest)
    {
        $request->validate([
            'samples' => 'required|array|min:1|max:4',
            'samples.*.url' => 'required|url',
            'samples.*.title' => 'nullable|string|max:255',
        ]);

        $samples = [];
        foreach ($request->samples as $index => $sample) {
            $samples[] = [
                'id' => $index + 1,
                'url' => $sample['url'],
                'title' => $sample['title'] ?? "Sample " . ($index + 1),
                'duration' => 15,
                'created_at' => now()->toISOString(),
            ];
        }

        $songRequest->update([
            'samples' => $samples,
            'samples_generated_at' => now(),
            'status' => 'samples_ready',
        ]);

        // Send email to customer
        if ($songRequest->email) {
            Mail::to($songRequest->email)->send(new SamplesReadyMail($songRequest, $samples));
        }

        return response()->json([
            'message' => 'Samples added and email sent',
            'data' => $this->formatRequest($songRequest, full: true),
        ]);
    }

    /**
     * Mark a sample as chosen (admin override)
     */
    public function markSampleChosen(Request $request, SongRequest $songRequest)
    {
        $request->validate([
            'sample_id' => 'required|integer|min:1|max:4',
        ]);

        $songRequest->update([
            'chosen_sample_id' => $request->sample_id,
            'status' => 'sample_chosen',
        ]);

        return response()->json([
            'message' => 'Sample marked as chosen',
            'data' => $this->formatRequest($songRequest),
        ]);
    }

    /**
     * Mark as released on Spotify
     */
    public function markReleased(Request $request, SongRequest $songRequest)
    {
        $request->validate([
            'spotify_uri' => 'nullable|string',
            'spotify_track_id' => 'nullable|string',
        ]);

        $songRequest->update([
            'spotify_uri' => $request->spotify_uri,
            'spotify_track_id' => $request->spotify_track_id,
            'status' => 'released',
            'released_at' => now(),
        ]);

        // TODO: Send "Your song is live!" email

        return response()->json([
            'message' => 'Marked as released',
            'data' => $this->formatRequest($songRequest),
        ]);
    }

    /**
     * Get dashboard stats
     */
    public function stats()
    {
        return response()->json([
            'pending_samples' => SongRequest::where('status', 'paid')->whereNull('samples')->count(),
            'awaiting_choice' => SongRequest::where('status', 'samples_ready')->count(),
            'ready_for_release' => SongRequest::where('status', 'sample_chosen')->count(),
            'released' => SongRequest::where('status', 'released')->count(),
            'total_paid' => SongRequest::whereIn('status', ['paid', 'samples_ready', 'sample_chosen', 'released'])->count(),
            'revenue_cents' => SongRequest::whereIn('status', ['paid', 'samples_ready', 'sample_chosen', 'released'])->sum('price_cents'),
        ]);
    }

    private function formatRequest(SongRequest $r, bool $full = false): array
    {
        $data = [
            'id' => $r->id,
            'category' => $r->category,
            'category_title' => $r->category_title,
            'recipient_name' => $r->recipient_name,
            'email' => $r->email,
            'status' => $r->status,
            'price_cents' => $r->price_cents,
            'created_at' => $r->created_at?->toISOString(),
            'has_samples' => $r->hasSamples(),
            'samples_count' => $r->samples ? count($r->samples) : 0,
            'chosen_sample_id' => $r->chosen_sample_id,
            'selection_url' => $r->selection_token
                ? url("/kies/{$r->selection_token}")
                : null,
        ];

        if ($full) {
            $data['intake'] = $r->intake;
            $data['lyrics'] = $r->lyrics;
            $data['lyrics_preview'] = $r->lyrics_preview;
            $data['final_lyrics'] = $r->final_lyrics;
            $data['music_prompt'] = $r->music_prompt;
            $data['samples'] = $r->samples;
            $data['samples_generated_at'] = $r->samples_generated_at?->toISOString();
            $data['spotify_uri'] = $r->spotify_uri;
            $data['spotify_track_id'] = $r->spotify_track_id;
            $data['released_at'] = $r->released_at?->toISOString();
        }

        return $data;
    }
}
