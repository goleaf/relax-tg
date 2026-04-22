<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTelegramApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.telegram.internal_api_token');

        if ($configuredToken === '') {
            return response()->json([
                'message' => 'Telegram API token is not configured.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $providedToken = (string) $request->bearerToken();

        if (($providedToken === '') || (! hash_equals($configuredToken, $providedToken))) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
