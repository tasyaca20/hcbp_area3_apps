<?php

use Illuminate\View\ViewServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

try {
    require dirname(__DIR__) . '/vendor/autoload.php';

    /** @var Application $app */
    $app = require dirname(__DIR__) . '/bootstrap/app.php';

    // Vercel's serverless bootstrap can skip Laravel's deferred view provider
    // when no cached provider manifest is present. Register it explicitly so
    // Blade/View bindings are always available before the first request.
    if (! $app->bound('view')) {
        $app->register(ViewServiceProvider::class);
    }

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    error_log($e->__toString());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo get_class($e) . ': ' . $e->getMessage();
}
