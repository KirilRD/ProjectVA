<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Explicit gate: manage tools (approve, reject, edit, delete) for Owner and Admin only.
        Gate::define('manage-tools', function ($user) {
            return $user->isOwner() || $user->isAdmin();
        });
    }
}
