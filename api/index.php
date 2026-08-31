<?php

// Vercel functions have an immutable deployment filesystem. Prepare writable
// runtime directories before Laravel boots, and provide the same key fallback
// as config/app.php when the Vercel environment has no APP_KEY yet.
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

require dirname(__DIR__) . '/public/index.php';
