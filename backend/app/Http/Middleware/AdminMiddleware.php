<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request. Allow only if the user can access admin area.
     * Owner: full access. Admin role: tools only (user management blocked by OwnerMiddleware).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->canAccessAdminArea()) {
            abort(403, __('You do not have permission to access this page.'));
        }

        return $next($request);
    }
}
