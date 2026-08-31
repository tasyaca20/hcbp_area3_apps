<?php

use App\Providers\AppServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withProviders([
        CacheServiceProvider::class, CookieServiceProvider::class, DatabaseServiceProvider::class,
        EncryptionServiceProvider::class, FilesystemServiceProvider::class, HashServiceProvider::class,
        PaginationServiceProvider::class, SessionServiceProvider::class, TranslationServiceProvider::class,
        ValidationServiceProvider::class, ViewServiceProvider::class, AppServiceProvider::class,
    ])
    ->registered(function (Application $app): void {
        $app->register(FilesystemServiceProvider::class, true);
        $app->register(ViewServiceProvider::class, true);
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
    })->create();
