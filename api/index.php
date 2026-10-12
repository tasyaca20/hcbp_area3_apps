<?php

// Vercel functions have an immutable deployment filesystem. Prepare writable
// runtime directories before Laravel boots.
$runtimeViewPath = '/tmp/laravel-views';
if (! is_dir($runtimeViewPath)) {
    @mkdir($runtimeViewPath, 0777, true);
}

if (! getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH='.$runtimeViewPath);
}

// Production database credentials are managed in Vercel Environment Variables.
if (getenv('VERCEL')) {
    putenv('APP_ENV=production');
    putenv('DB_CONNECTION=pgsql');
}

require dirname(__DIR__) . '/public/index.php';
