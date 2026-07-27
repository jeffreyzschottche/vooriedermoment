<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendSamplesReadyNotification;
use App\Models\SongRequest;
use App\Services\Orders\SamplePayloadFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class AutomationSampleController extends Controller
{
    public function store(
        Request $request,
        SongRequest $songRequest,
        SamplePayloadFactory $payloads,
    ): JsonResponse {
        $claimToken = $this->claimToken($request);
        $this->assertClaimToken($songRequest, $claimToken, allowCompleted: true);

        if ($songRequest->automation_status === 'completed' && $songRequest->songSamples()->count() === 4) {
            return response()->json([
                'data' => $this->responseData($songRequest->load('songSamples'), $payloads),
            ]);
        }

        $validated = $request->validate([
            'samples' => ['required', 'array', 'size:4'],
            'samples.*.position' => ['required', 'integer', 'between:1,4', 'distinct'],
            'samples.*.title' => ['required', 'string', 'max:255'],
            'samples.*.suno_source_url' => ['required', 'url', 'max:2000'],
            'samples.*.preview' => ['required', 'file', 'mimes:mp3', 'max:20480'],
            'samples.*.original' => ['required', 'file', 'mimes:mp3,wav,m4a', 'max:102400'],
            'samples.*.cover' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $positions = collect($validated['samples'])->pluck('position')->sort()->values()->all();
        if ($positions !== [1, 2, 3, 4]) {
            throw ValidationException::withMessages([
                'samples' => 'De vier posities 1, 2, 3 en 4 zijn verplicht.',
            ]);
        }

        $disk = (string) config('orders.storage_disk', 'local');
        $storedPaths = [];
        $rows = [];

        try {
            foreach ($validated['samples'] as $index => $sample) {
                $position = (int) $sample['position'];
                $directory = "orders/{$songRequest->id}/samples/{$position}";

                $previewPath = $this->storeFile(
                    $request->file("samples.{$index}.preview"),
                    $directory,
                    'preview.mp3',
                    $disk,
                );
                $original = $request->file("samples.{$index}.original");
                $originalPath = $this->storeFile(
                    $original,
                    $directory,
                    'original.'.strtolower($original->getClientOriginalExtension()),
                    $disk,
                );
                $cover = $request->file("samples.{$index}.cover");
                $coverPath = $this->storeFile(
                    $cover,
                    $directory,
                    'cover.'.strtolower($cover->getClientOriginalExtension()),
                    $disk,
                );

                array_push($storedPaths, $previewPath, $originalPath, $coverPath);
                $rows[] = [
                    'position' => $position,
                    'title' => $sample['title'],
                    'storage_disk' => $disk,
                    'preview_path' => $previewPath,
                    'original_audio_path' => $originalPath,
                    'cover_path' => $coverPath,
                    'suno_source_url' => $sample['suno_source_url'],
                    'expires_at' => now()->addDays((int) config('orders.sample_retention_days', 14)),
                ];
            }

            DB::transaction(function () use ($songRequest, $claimToken, $rows): void {
                $claimedOrder = SongRequest::whereKey($songRequest->id)->lockForUpdate()->firstOrFail();
                $this->assertClaimToken($claimedOrder, $claimToken);

                if ($claimedOrder->songSamples()->exists()) {
                    throw new RuntimeException('Voor deze order zijn al samples opgeslagen.');
                }

                foreach ($rows as $row) {
                    $claimedOrder->songSamples()->create($row);
                }

                $claimedOrder->forceFill([
                    'status' => 'samples_ready',
                    'samples_generated_at' => now(),
                    'automation_status' => 'completed',
                    'automation_claim_expires_at' => null,
                    'automation_last_error' => null,
                    'exported_at' => now(),
                ])->save();

                SendSamplesReadyNotification::dispatch($claimedOrder->id)->afterCommit();
            });
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($storedPaths);
            throw $exception;
        }

        $songRequest->refresh()->load('songSamples');

        return response()->json([
            'message' => 'Vier samples opgeslagen; klantmail staat in de queue.',
            'data' => $this->responseData($songRequest, $payloads),
        ], 201);
    }

    private function responseData(SongRequest $songRequest, SamplePayloadFactory $payloads): array
    {
        return [
            'order_id' => $songRequest->id,
            'status' => $songRequest->status,
            'automation_status' => $songRequest->automation_status,
            'samples' => $payloads->forSelection($songRequest),
        ];
    }

    private function storeFile($file, string $directory, string $filename, string $disk): string
    {
        $path = $file->storeAs($directory, $filename, $disk);

        if (! $path) {
            throw new RuntimeException("Opslaan van {$filename} is mislukt.");
        }

        return $path;
    }

    private function claimToken(Request $request): string
    {
        return (string) ($request->header('X-Claim-Token') ?: $request->input('claim_token'));
    }

    private function assertClaimToken(
        SongRequest $songRequest,
        string $token,
        bool $allowCompleted = false,
    ): void {
        $validStatuses = $allowCompleted ? ['claimed', 'completed'] : ['claimed'];
        $validHash = (string) $songRequest->automation_claim_token_hash;

        abort_unless(
            in_array($songRequest->automation_status, $validStatuses, true)
                && $token !== ''
                && $validHash !== ''
                && hash_equals($validHash, hash('sha256', $token)),
            409,
            'Deze automation-claim is ongeldig of niet meer actief.',
        );

        if ($songRequest->automation_status === 'claimed') {
            abort_if(
                $songRequest->automation_claim_expires_at?->isPast(),
                409,
                'Deze automation-claim is verlopen.',
            );
        }
    }
}
