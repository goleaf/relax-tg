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
                'message' => __('telegram.api.token_not_configured'),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $providedToken = (string) $request->bearerToken();

        if (($providedToken === '') || (! hash_equals($configuredToken, $providedToken))) {
            return response()->json([
                'message' => __('http-statuses.401'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
