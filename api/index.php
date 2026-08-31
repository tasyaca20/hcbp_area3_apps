<?php

// Vercel functions have an immutable deployment filesystem. Prepare writable
// runtime directories before Laravel boots.
$runtimeViewPath = '/tmp/laravel-views';
if (! is_dir($runtimeViewPath)) {
    @mkdir($runtimeViewPath, 0777, true);
}

if (! getenv('APP_KEY')) {
    putenv('APP_KEY=base64:Q7pVY9g5t3H2m8L4c6N1s0KzR5xW2bF9dJ7hG3qP8aM=');
}
if (! getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH='.$runtimeViewPath);
}

// Pin Vercel production to the public Railway MySQL TCP proxy. These are
// non-secret connection coordinates; the password remains in Vercel env.
if (getenv('VERCEL')) {
    putenv('APP_ENV=production');
    putenv('DB_CONNECTION=mysql');
    putenv('DB_HOST=sakura.proxy.rlwy.net');
    putenv('DB_PORT=10132');
    putenv('DB_DATABASE=railway');
    putenv('DB_USERNAME=root');
    // A stale DB_URL can override host/port in Laravel's database manager.
    putenv('DB_URL=');
}

// Support the common Railway secret names when DB_PASSWORD is not present.
if (! getenv('DB_PASSWORD')) {
    if (getenv('MYSQLPASSWORD')) {
        putenv('DB_PASSWORD='.getenv('MYSQLPASSWORD'));
    } elseif (getenv('MYSQL_ROOT_PASSWORD')) {
        putenv('DB_PASSWORD='.getenv('MYSQL_ROOT_PASSWORD'));
    }
}

require dirname(__DIR__) . '/public/index.php';
