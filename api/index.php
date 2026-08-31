<?php

try {
    require dirname(__DIR__) . '/public/index.php';
} catch (Throwable $e) {
    error_log($e->__toString());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo get_class($e) . ': ' . $e->getMessage();
}
