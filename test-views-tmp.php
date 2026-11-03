<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$views = [
    'admin-master.idp.daftar',
    'admin-master.idp.penetapan',
    'admin-master.idp.pemantauan',
    'admin-master.coaching.pemantauan',
    'admin-master.idp.evaluasi',
];

$admin = \App\Models\Pengguna::where('role', 'admin_master')->first();
if (! $admin) {
    echo 'No admin_master user found'.PHP_EOL;
    exit(1);
}
auth()->login($admin);
echo 'Logged in as: '.$admin->nama.' ('.$admin->role.')'.PHP_EOL;
echo 'Notifications relation ok: '.(method_exists($admin, 'notifikasi') ? 'yes' : 'no').PHP_EOL;

foreach ($views as $view) {
    try {
        $html = view($view, [
            'rows' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
            'summaryRows' => collect(),
            'jabatan' => collect(),
            'unitInduk' => collect(),
            'cardTitle' => 'Test',
        ])->render();

        // crude structural check
        $openTable = substr_count($html, '<table');
        $closeTable = substr_count($html, '</table>');
        $openThead = substr_count($html, '<thead');
        $closeThead = substr_count($html, '</thead>');
        $openTr = substr_count($html, '<tr');
        $closeTr = substr_count($html, '</tr>');
        $hasExport = str_contains($html, 'Export') && preg_match('/href="[^"]*export[^"]*"/i', $html) === 1;
        preg_match_all('/href="([^"]*export[^"]*)"/i', $html, $m);
        printf(
            "%-38s table %d/%d  thead %d/%d  tr %d/%d  exportBtn=%s\n",
            $view,
            $openTable, $closeTable,
            $openThead, $closeThead,
            $openTr, $closeTr,
            $hasExport ? 'YES' : 'NO'
        );
        if (! empty($m[1])) {
            echo '   exportHref: '.implode(', ', array_slice($m[1], 0, 3)).PHP_EOL;
        }
    } catch (\Throwable $e) {
        echo str_pad($view, 38).' FAIL: '.get_class($e).': '.$e->getMessage().PHP_EOL;
    }
}
