<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Vercel PHP functions run from a read-only deployment filesystem. Laravel's
// generated framework manifests must therefore live in /tmp at runtime.
if (getenv('VERCEL') || getenv('VERCEL_ENV')) {
    $runtimeCache = '/tmp/laravel-bootstrap-cache';

    if (! is_dir($runtimeCache)) {
        @mkdir($runtimeCache, 0775, true);
    }

    putenv("APP_PACKAGES_CACHE={$runtimeCache}/packages.php");
    putenv("APP_SERVICES_CACHE={$runtimeCache}/services.php");
    putenv("APP_CONFIG_CACHE={$runtimeCache}/config.php");
    putenv("APP_ROUTES_CACHE={$runtimeCache}/routes.php");
    putenv("APP_EVENTS_CACHE={$runtimeCache}/events.php");
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
    })->create();
