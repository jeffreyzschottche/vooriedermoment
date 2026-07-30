<?php

namespace App\Http\Controllers;

use App\Mail\SamplesAvailableMail;
use App\Models\SongRequest;
use App\Services\Audio\AudioPreviewClipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

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

    public function store(Request $request, string $token, AudioPreviewClipper $clipper)
    {
        $songRequest = SongRequest::where('admin_upload_token', $token)->firstOrFail();

        $validated = $request->validate([
            'samples' => 'required|array|size:4',
            'samples.*.title' => 'required|string|max:255',
            'samples.*.audio' => 'required|file|mimes:mp3,wav,m4a|max:20480',
            'samples.*.cover' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'samples.*.suno_url' => 'required|url',
        ]);

        $disk = Storage::disk('public');
        $newPaths = [];
        $rows = [];

        try {
            foreach ($validated['samples'] as $position => $sampleData) {
                $pos = $position + 1;
                $temporaryPreview = $clipper->clipToTemporaryFile($sampleData['audio']);
                $audioPath = "samples/{$songRequest->id}/preview-{$pos}-".Str::random(12).'.mp3';
                $previewStream = fopen($temporaryPreview, 'rb');

                try {
                    if ($previewStream === false || ! $disk->put($audioPath, $previewStream)) {
                        throw new RuntimeException("Preview {$pos} kon niet worden opgeslagen.");
                    }
                } finally {
                    if (is_resource($previewStream)) {
                        fclose($previewStream);
                    }
                    @unlink($temporaryPreview);
                }
                $newPaths[] = $audioPath;

                $coverFile = $sampleData['cover'];
                $coverPath = $coverFile->store("samples/{$songRequest->id}/covers", 'public');
                if (! $coverPath) {
                    throw new RuntimeException("Cover {$pos} kon niet worden opgeslagen.");
                }

                $newPaths[] = $coverPath;
                $rows[] = [
                    'position' => $pos,
                    'title' => $sampleData['title'],
                    'storage_disk' => 'public',
                    'preview_path' => $audioPath,
                    'cover_path' => $coverPath,
                    'suno_source_url' => $sampleData['suno_url'],
                    'expires_at' => now()->addDays((int) config('orders.sample_retention_days', 14)),
                ];
            }

            $oldSamples = $songRequest->songSamples()->get();

            DB::transaction(function () use ($songRequest, $rows): void {
                $songRequest->songSamples()->delete();

                foreach ($rows as $row) {
                    $songRequest->songSamples()->create($row);
                }

                $songRequest->forceFill([
                    'samples_generated_at' => now(),
                    'status' => 'samples_ready',
                ])->save();
            });

            foreach ($oldSamples as $sample) {
                Storage::disk($sample->storage_disk)->delete(array_filter([
                    $sample->preview_path,
                    $sample->cover_path,
                ]));
            }
        } catch (Throwable $exception) {
            $disk->delete($newPaths);
            throw $exception;
        }

        return redirect()->route('admin.upload.show', ['token' => $token])
            ->with('success', 'Vier previews van 00:30–00:45 opgeslagen. Klik op "Mail naar klant" om de klant te notificeren.');
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
