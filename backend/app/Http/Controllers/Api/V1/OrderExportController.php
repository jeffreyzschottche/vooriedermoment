<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Pull-export voor de lokale Suno-macro.
 *
 * Flow:
 * 1. GET  /api/v1/orders/export        -> alle betaalde, nog niet opgehaalde JSONs
 * 2. (macro genereert de nummers op suno.com)
 * 3. POST /api/v1/orders/export/ack    -> markeer als opgehaald + verwijder van server
 *
 * Beveiligd via X-Export-Key (ExportKeyMiddleware).
 */
class OrderExportController extends Controller
{
    public function __construct(private OrderExporter $exporter)
    {
    }

    /**
     * Alle betaalde aanvragen die nog niet zijn opgehaald.
     */
    public function index(): JsonResponse
    {
        $orders = SongRequest::whereNull('exported_at')
            ->whereIn('status', ['paid', 'producing', 'music_prompt_ready', 'production_ready'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (SongRequest $r) => $this->exporter->buildPayload($r))
            ->values();

        return response()->json([
            'count' => $orders->count(),
            'orders' => $orders,
        ]);
    }

    /**
     * Bevestig dat de macro deze aanvragen heeft opgehaald: markeer als
     * geëxporteerd en verwijder het lokale JSON-bestand van de server.
     */
    public function ack(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $requests = SongRequest::whereIn('id', $validated['ids'])
            ->whereNull('exported_at')
            ->get();

        foreach ($requests as $songRequest) {
            if ($songRequest->export_path && File::exists($songRequest->export_path)) {
                File::delete($songRequest->export_path);
            }

            $songRequest->forceFill([
                'exported_at' => now(),
                'export_path' => null,
            ])->saveQuietly();
        }

        return response()->json([
            'acknowledged' => $requests->pluck('id')->values(),
            'count' => $requests->count(),
        ]);
    }
}
