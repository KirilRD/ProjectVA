<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    /**
     * Handle an incoming request. Allow only if the user can manage users (Owner role only).
     * Admin role can manage tools but gets 403 here when accessing user management.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->canManageUsers()) {
            abort(403, __('You do not have permission to access this page. Only owners can manage users.'));
        }

        return $next($request);
    }
}
