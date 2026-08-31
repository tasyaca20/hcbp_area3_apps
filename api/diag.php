<?php

header('Content-Type: text/plain; charset=utf-8');
require dirname(__DIR__) . '/vendor/autoload.php';
echo 'view-class=' . (class_exists(\Illuminate\View\ViewServiceProvider::class) ? 'yes' : 'no') . PHP_EOL;
echo 'app-class=' . (class_exists(\Illuminate\Foundation\Application::class) ? 'yes' : 'no') . PHP_EOL;
echo 'php=' . PHP_VERSION . PHP_EOL;
