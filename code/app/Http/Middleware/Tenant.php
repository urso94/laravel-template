<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Tenant
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$tenant = $request->header('x-tenant')) {
            return response()->json(['message' => 'Tenant not provided'], 503);
        }

        if (!\App\Models\Tenant::where('code', '=', $tenant)->first()) {
            return response()->json(['message' => 'Tenant not configured'], 503);
        }

        return $next($request);
    }
}
