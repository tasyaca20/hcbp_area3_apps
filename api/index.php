<?php

// Vercel functions have an immutable deployment filesystem. Prepare writable
// runtime directories before Laravel boots, and provide safe production
// defaults when the hosting environment does not inject Laravel DB aliases.
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

// Ensure Vercel production uses the Railway MySQL TCP proxy. These values are
// non-secret; the password must remain a Vercel Environment Variable.
$runtimeEnv = [
    'APP_ENV' => 'production',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => 'sakura.proxy.rlwy.net',
    'DB_PORT' => '10132',
    'DB_DATABASE' => 'railway',
    'DB_USERNAME' => 'root',
];
foreach ($runtimeEnv as $key => $value) {
    if (! getenv($key)) {
        putenv($key.'='.$value);
    }
}

// Railway commonly names the secret MYSQLPASSWORD. Support it transparently
// when DB_PASSWORD was not separately configured in Vercel.
if (! getenv('DB_PASSWORD') && getenv('MYSQLPASSWORD')) {
    putenv('DB_PASSWORD='.getenv('MYSQLPASSWORD'));
}

require dirname(__DIR__) . '/public/index.php';
