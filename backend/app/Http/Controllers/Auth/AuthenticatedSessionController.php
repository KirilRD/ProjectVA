<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Preserve intended URL before any further session use (e.g. for after 2FA).
        $intended = $request->session()->get('url.intended');
        if ($intended !== null) {
            $request->session()->put('url.intended', $intended);
        }

        // Generate a new random 6-digit code on every login (never reuse).
        $code = (string) random_int(100000, 999999);
        Log::info('2FA Code for ' . $user->email . ': ' . $code);
        $user->forceFill([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(15),
        ])->save();

        $user->notify(new TwoFactorCodeNotification($code));

        // Force session write so auth + intended are persisted before redirect (avoids double login).
        $request->session()->save();

        return redirect()->route('verify-2fa');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ])->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
