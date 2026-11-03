<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\DaftarIdpExport;
use App\Exports\EvaluasiIdpExport;
use App\Exports\PemantauanCoachingExport;
use App\Exports\PemantauanIdpExport;
use App\Exports\PenetapanIdpExport;
use Maatwebsite\Excel\Facades\Excel;

echo 'DB: '.config('database.connections.'.config('database.default').'.database').PHP_EOL;
echo 'IDP rows: '.\DB::table('daftar_idp')->count().PHP_EOL;

$out = __DIR__.'/storage/app/export-test';
if (! is_dir($out)) {
    mkdir($out, 0777, true);
}

$cases = [
    'daftar' => new DaftarIdpExport(null),
    'daftar-search' => new DaftarIdpExport('UID'),
    'penetapan' => new PenetapanIdpExport(null),
    'pemantauan' => new PemantauanIdpExport(null),
    'coaching' => new PemantauanCoachingExport(null),
    'evaluasi' => new EvaluasiIdpExport(null),
];

foreach ($cases as $name => $export) {
    try {
        $file = $out.'/'.$name.'.xlsx';
        Excel::store($export, 'export-test/'.$name.'.xlsx', 'local');
        $path = storage_path('app/private/export-test/'.$name.'.xlsx');
        if (! file_exists($path)) {
            $path = storage_path('app/export-test/'.$name.'.xlsx');
        }
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet();
        echo str_pad($name, 16).' OK  rows='.($sheet->getHighestRow() - 1).'  cols='.$sheet->getHighestColumn().'  bytes='.filesize($path).PHP_EOL;
        echo '   header: '.implode(' | ', $sheet->rangeToArray('A1:'.$sheet->getHighestColumn().'1')[0]).PHP_EOL;
        if ($sheet->getHighestRow() > 1) {
            echo '   row2  : '.implode(' | ', array_map(fn ($v) => is_scalar($v) ? (string) $v : '', $sheet->rangeToArray('A2:'.$sheet->getHighestColumn().'2')[0])).PHP_EOL;
        }
    } catch (\Throwable $e) {
        echo str_pad($name, 16).' FAIL: '.get_class($e).': '.$e->getMessage().PHP_EOL;
    }
}
