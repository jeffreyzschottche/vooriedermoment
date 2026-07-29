<?php

namespace App\Http\Controllers;

use App\Mail\SamplesAvailableMail;
use App\Models\SongRequest;
use App\Models\SongSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminUploadController extends Controller
{
    public function show(string $token)
    {
        $songRequest = SongRequest::where('admin_upload_token', $token)->firstOrFail();

        return view('admin.upload', [
            'songRequest' => $songRequest,
            'existingSamples' => $songRequest->songSamples,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $songRequest = SongRequest::where('admin_upload_token', $token)->firstOrFail();

        $validated = $request->validate([
            'samples' => 'required|array|size:4',
            'samples.*.title' => 'required|string|max:255',
            'samples.*.audio' => 'required|file|mimes:mp3,wav,m4a|max:20480',
            'samples.*.cover' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'samples.*.suno_url' => 'required|url',
        ]);

        // Verwijder bestaande samples
        foreach ($songRequest->songSamples as $sample) {
            if ($sample->preview_path && Storage::disk($sample->storage_disk)->exists($sample->preview_path)) {
                Storage::disk($sample->storage_disk)->delete($sample->preview_path);
            }
            if ($sample->cover_path && Storage::disk($sample->storage_disk)->exists($sample->cover_path)) {
                Storage::disk($sample->storage_disk)->delete($sample->cover_path);
            }
            $sample->delete();
        }

        $expiresAt = now()->addDays(14);

        // Maak nieuwe samples aan
        foreach ($validated['samples'] as $position => $sampleData) {
            $pos = $position + 1;

            // Upload audio preview
            $audioFile = $sampleData['audio'];
            $audioPath = $audioFile->store("samples/{$songRequest->id}", 'public');

            // Upload cover
            $coverFile = $sampleData['cover'];
            $coverPath = $coverFile->store("samples/{$songRequest->id}/covers", 'public');

            SongSample::create([
                'song_request_id' => $songRequest->id,
                'position' => $pos,
                'title' => $sampleData['title'],
                'storage_disk' => 'public',
                'preview_path' => $audioPath,
                'cover_path' => $coverPath,
                'suno_source_url' => $sampleData['suno_url'],
                'expires_at' => $expiresAt,
            ]);
        }

        $songRequest->update([
            'samples_generated_at' => now(),
            'status' => 'samples_ready',
        ]);

        return redirect()->route('admin.upload.show', ['token' => $token])
            ->with('success', 'Samples opgeslagen! Klik op "Mail naar klant" om de klant te notificeren.');
    }

    public function sendToCustomer(string $token)
    {
        $songRequest = SongRequest::where('admin_upload_token', $token)->firstOrFail();

        if ($songRequest->songSamples->count() !== 4) {
            return back()->with('error', 'Upload eerst 4 samples voordat je de klant mailt.');
        }

        // Genereer nieuw wachtwoord
        $songRequest->generateCustomerPassword();

        // Stuur mail naar klant
        Mail::to($songRequest->email)->send(new SamplesAvailableMail($songRequest));

        $songRequest->update([
            'samples_email_sent_at' => now(),
        ]);

        return back()->with('success', 'E-mail verstuurd naar ' . $songRequest->email);
    }
}
