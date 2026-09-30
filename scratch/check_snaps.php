<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('project_api_snapshots')
    ->select('project_id', 'media', 'endpoint_key', Illuminate\Support\Facades\DB::raw('count(*) as c'))
    ->groupBy('project_id', 'media', 'endpoint_key')
    ->get();

echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
