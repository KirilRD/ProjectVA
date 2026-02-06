<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if ($user && filled($user->two_factor_code ?? null)) {
                return route('verify-2fa');
            }
            return route('dashboard');
        });

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'owner' => \App\Http\Middleware\OwnerMiddleware::class,
            'two_factor' => \App\Http\Middleware\TwoFactorMiddleware::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'redirect_if_2fa_verified' => \App\Http\Middleware\RedirectIf2FAVerified::class,
            'disable.back.cache' => \App\Http\Middleware\DisableBackCache::class,
        ]);

        $middleware->web(append: [\App\Http\Middleware\PreventCacheMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
