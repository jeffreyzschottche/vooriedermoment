<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    /**
     * Simple token-based admin authentication.
     * Add ADMIN_TOKEN to your .env file.
     *
     * Usage: Add header "X-Admin-Token: your-secret-token"
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Admin-Token');
        $validToken = config('app.admin_token');

        if (!$validToken) {
            // If no admin token configured, block all admin routes
            return response()->json([
                'error' => 'Admin access not configured. Add ADMIN_TOKEN to .env',
            ], 500);
        }

        if ($token !== $validToken) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
