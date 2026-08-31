<?php

use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

try {
    require dirname(__DIR__) . '/vendor/autoload.php';

    $app = require dirname(__DIR__) . '/bootstrap/app.php';

    // Vercel's serverless bootstrap can run with an incomplete provider manifest.
    // Register the filesystem first because ViewServiceProvider depends on it.
    $app->register(FilesystemServiceProvider::class, true);
    $app->register(ViewServiceProvider::class, true);

    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (Throwable $e) {
    error_log($e->__toString());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo get_class($e) . ': ' . $e->getMessage();
}
