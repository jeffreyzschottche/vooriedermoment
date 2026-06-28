<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Beveiligt de order-export-endpoints met een geheime sleutel.
 *
 * Gebruik: stuur header "X-Export-Key: <ORDERS_API_KEY>" mee.
 */
class ExportKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $validKey = config('orders.api_key');

        if (! $validKey) {
            return response()->json([
                'error' => 'Export niet geconfigureerd. Zet ORDERS_API_KEY in .env',
            ], 500);
        }

        $key = $request->header('X-Export-Key') ?: $request->query('key');

        if (! $key || ! hash_equals($validKey, (string) $key)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
