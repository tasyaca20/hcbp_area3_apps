<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

$paths = [
    'coaching-evidence/14/6rudmzSon0Y9hMccG5u9XGBuJzm2VqyrxN3RWw7B.pdf',
    'coaching-evidence/14/DMwEYvtDASb1yOdsJdQzsXfnywqBGOLA6qjoCcJh.pdf',
    'coaching-evidence/14/rc6LLQKzi3uQijulNM75gtz4wS8jTb1yMlpSvNjb.pdf',
];

foreach ($paths as $path) {
    $exists = Storage::disk('public')->exists($path);
    $url = Storage::disk('public')->url($path);
    echo "Path: $path\n";
    echo "Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    echo "URL: $url\n";
    echo "Public Path: " . storage_path('app/public/' . $path) . "\n";
    echo str_repeat('-', 80) . "\n";
}
