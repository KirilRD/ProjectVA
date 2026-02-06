<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * If the user has two_factor_code set in the database, they MUST be redirected
     * to the verification page for every request until verified (no dashboard access).
     * Do NOT redirect authenticated users back to /login when they are on verify-2fa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $user->refresh();

        if ($user->is_active === false) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', __('Вашият акаунт е деактивиран. Моля, свържете се с администратор.'));
        }

        // User is authenticated and active. If 2FA is pending, only allow verify-2fa and logout.
        if (filled($user->two_factor_code)) {
            if ($request->is('verify-2fa') || $request->is('verify-2fa/*') || $request->is('logout') || $request->is('logout-inactive')) {
                return $next($request);
            }
            return redirect()->to('/verify-2fa');
        }

        return $next($request);
    }
}
