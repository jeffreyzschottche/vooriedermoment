<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Beveiligt de automation-endpoints met een geheime sleutel.
 *
 * Gebruik: stuur header "X-Automation-Key: <AUTOMATION_API_KEY>" mee.
 */
class ExportKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $validKey = config('orders.api_key');

        if (! $validKey) {
            return response()->json([
                'error' => 'Automation niet geconfigureerd. Zet AUTOMATION_API_KEY in .env',
            ], 500);
        }

        $key = $request->header('X-Automation-Key')
            ?: $request->header('X-Export-Key');

        if (! $key || ! hash_equals($validKey, (string) $key)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
