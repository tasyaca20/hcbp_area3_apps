<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the native filesystem early so Blade/ViewFinder is available
        // during the serverless bootstrap phase on Vercel.
        if (! $this->app->bound('files')) {
            $this->app->singleton('files', fn () => new Filesystem);
        }
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
