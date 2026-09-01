<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('daftar_idp');
echo "Columns in daftar_idp:\n";
foreach ($columns as $col) {
    $type = Schema::getColumnType('daftar_idp', $col);
    echo "- $col ($type)\n";
}
