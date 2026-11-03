<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'evaluasi rows: '.\DB::table('evaluasi')->count().PHP_EOL;
echo 'evaluasi_bukti rows: '.\DB::table('evaluasi_bukti')->count().PHP_EOL;
echo 'rencana_pengembangan_idp total: '.\DB::table('rencana_pengembangan_idp')->count().PHP_EOL;
echo 'rencana status Disetujui: '.\DB::table('rencana_pengembangan_idp')->where('status', 'Disetujui')->count().PHP_EOL;
echo 'coaching_bukti: '.\DB::table('coaching_bukti')->count().PHP_EOL;
echo 'monitoring_idp: '.\DB::table('monitoring_idp')->count().PHP_EOL;
echo 'monitoring_coaching: '.\DB::table('monitoring_coaching')->count().PHP_EOL;
