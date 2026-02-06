<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIf2FAVerified
{
    /**
     * If the user is authenticated and has already passed 2FA (no code pending),
     * redirect to dashboard so they cannot use Back to reach verify-2fa again.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $user->refresh();

        if (! filled($user->two_factor_code)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
