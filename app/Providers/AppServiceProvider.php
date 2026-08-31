<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\View\ViewServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the view services explicitly. This keeps the Laravel view
        // container available in Vercel's serverless runtime even when the
        // framework's cached provider manifest is rebuilt differently.
        (new ViewServiceProvider($this->app))->register();
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
