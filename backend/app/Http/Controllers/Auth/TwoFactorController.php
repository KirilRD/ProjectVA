<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA code verification form.
     */
    public function index(): View
    {
        return view('auth.verify-2fa');
    }

    /**
     * Verify the 2FA code and clear it on success.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if ($user->two_factor_code !== $request->input('code')) {
            return back()->withErrors(['code' => __('The verification code is invalid.')]);
        }

        if ($user->two_factor_expires_at && $user->two_factor_expires_at->isPast()) {
            return back()->withErrors(['code' => __('The verification code has expired. Please log in again.')]);
        }

        // Clear code immediately so it cannot be used again (one-time use).
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return redirect()->intended(route('dashboard', absolute: false))->with('status', __('Two-factor verification successful.'));
    }
}
