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

// Production connection settings belong in Vercel environment variables.
// Do not keep APP_KEY or database credentials in source control.
if (getenv('VERCEL')) {
    putenv('APP_ENV=production');
    putenv('DB_CONNECTION=mysql');
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
