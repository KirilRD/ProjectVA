<?php

namespace App\Providers;

use App\Models\Tool;
use App\Policies\ToolPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Tool::class, ToolPolicy::class);

        // Ensure SQLite database file exists when using sqlite (avoids "could not find driver" confusion)
        if (config('database.default') === 'sqlite' && extension_loaded('pdo_sqlite')) {
            $path = config('database.connections.sqlite.database');
            if ($path && ! file_exists($path)) {
                touch($path);
            }
        }
    }
}
