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

// This application is intentionally pinned to the public Railway MySQL TCP
// proxy in Vercel production. These are non-secret connection coordinates;
// the password remains a Vercel Environment Variable.
if (getenv('VERCEL')) {
    putenv('APP_ENV=production');
    putenv('DB_CONNECTION=mysql');
    putenv('DB_HOST=sakura.proxy.rlwy.net');
    putenv('DB_PORT=10132');
    putenv('DB_DATABASE=railway');
    putenv('DB_USERNAME=root');
}

// Railway commonly names the secret MYSQLPASSWORD. Support it when
// DB_PASSWORD is not separately configured in Vercel.
if (! getenv('DB_PASSWORD') && getenv('MYSQLPASSWORD')) {
    putenv('DB_PASSWORD='.getenv('MYSQLPASSWORD'));
}

require dirname(__DIR__) . '/public/index.php';
