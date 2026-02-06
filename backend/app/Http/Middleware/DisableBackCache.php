<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableBackCache
{
    /**
     * Add no-cache headers so the browser does not serve cached content on Back.
     * Prevents sensitive pages (auth, 2fa, dashboard) from being shown from cache after logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
