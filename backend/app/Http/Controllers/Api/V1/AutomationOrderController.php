<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutomationOrderController extends Controller
{
    public function claim(Request $request, OrderExporter $exporter): JsonResponse
    {
        $validated = $request->validate([
            'worker_id' => ['required', 'string', 'max:100'],
            'admin_upload_token' => ['nullable', 'string', 'max:64'],
        ]);

        $result = DB::transaction(function () use ($validated, $exporter): ?array {
            $query = SongRequest::query()
                ->whereNotNull('paid_at')
                ->whereIn('status', ['music_prompt_ready', 'production_ready'])
                ->where(function ($query) {
                    $query
                        ->where('automation_status', 'ready')
                        ->orWhere(function ($expired) {
                            $expired
                                ->where('automation_status', 'claimed')
                                ->where('automation_claim_expires_at', '<=', now());
                        });
                });

            if (! empty($validated['admin_upload_token'])) {
                $query->where('admin_upload_token', $validated['admin_upload_token']);
            } else {
                $query->orderBy('paid_at')->orderBy('id');
            }

            $songRequest = $query->lockForUpdate()->first();

            if (! $songRequest) {
                return null;
            }

            $claimToken = Str::random(64);
            $expiresAt = now()->addMinutes((int) config('orders.claim_ttl_minutes', 60));

            $songRequest->forceFill([
                'automation_status' => 'claimed',
                'automation_claimed_by' => $validated['worker_id'],
                'automation_claim_token_hash' => hash('sha256', $claimToken),
                'automation_claimed_at' => now(),
                'automation_claim_expires_at' => $expiresAt,
                'automation_attempts' => $songRequest->automation_attempts + 1,
                'automation_last_error' => null,
            ])->save();

            return [
                'order' => $exporter->buildPayload($songRequest->refresh()),
                'claim_token' => $claimToken,
                'claimed_by' => $validated['worker_id'],
                'claim_expires_at' => $expiresAt->toIso8601String(),
            ];
        });

        return response()->json(['data' => $result]);
    }

    public function complete(Request $request, SongRequest $songRequest): JsonResponse
    {
        DB::transaction(function () use ($request, $songRequest): void {
            $claimedOrder = SongRequest::whereKey($songRequest->id)->lockForUpdate()->firstOrFail();
            $this->validateClaim($request, $claimedOrder);

            $claimedOrder->forceFill([
                'automation_status' => 'completed',
                'automation_claim_token_hash' => null,
                'automation_claim_expires_at' => null,
                'automation_last_error' => null,
                'exported_at' => now(),
            ])->save();
        });

        return response()->json([
            'data' => [
                'order_id' => $songRequest->id,
                'automation_status' => 'completed',
            ],
        ]);
    }

    public function fail(Request $request, SongRequest $songRequest): JsonResponse
    {
        $validated = $request->validate([
            'error' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $songRequest, $validated): void {
            $claimedOrder = SongRequest::whereKey($songRequest->id)->lockForUpdate()->firstOrFail();
            $this->validateClaim($request, $claimedOrder);

            $claimedOrder->forceFill([
                'automation_status' => 'ready',
                'automation_claimed_by' => null,
                'automation_claim_token_hash' => null,
                'automation_claimed_at' => null,
                'automation_claim_expires_at' => null,
                'automation_last_error' => $validated['error'],
            ])->save();
        });

        return response()->json([
            'data' => [
                'order_id' => $songRequest->id,
                'automation_status' => 'ready',
                'retryable' => true,
            ],
        ]);
    }

    private function validateClaim(Request $request, SongRequest $songRequest): void
    {
        $token = (string) ($request->header('X-Claim-Token') ?: $request->input('claim_token'));
        $validHash = (string) $songRequest->automation_claim_token_hash;

        abort_unless(
            $songRequest->automation_status === 'claimed'
                && $token !== ''
                && $validHash !== ''
                && hash_equals($validHash, hash('sha256', $token)),
            409,
            'Deze automation-claim is ongeldig of niet meer actief.',
        );

        abort_if(
            $songRequest->automation_claim_expires_at?->isPast(),
            409,
            'Deze automation-claim is verlopen.',
        );
    }
}
