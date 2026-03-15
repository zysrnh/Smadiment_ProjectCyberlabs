<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$c = app(\App\Services\MediaKernelsClient::class);
$posts = $c->ytbTopStatus('17143', '2026-03-01', '2026-03-12', 0, 23, 500, 'postbyview');

echo "Total posts: " . count($posts) . PHP_EOL;

$hc = [];
foreach ($posts as $p) {
    if (!is_array($p)) continue;
    $ct = $p['content'] ?? $p['caption'] ?? $p['text'] ?? $p['name'] ?? '';
    preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $ct, $m);
    foreach ($m[1] as $t) {
        $t = strtolower(trim($t));
        if (strlen($t) < 2) continue;
        $hc[$t] = ($hc[$t] ?? 0) + 1;
    }
}
arsort($hc);
echo "Total hashtags found: " . count($hc) . PHP_EOL;
echo "Top 10: " . json_encode(array_slice($hc, 0, 10, true)) . PHP_EOL;

if (count($hc) === 0) {
    echo "\nSample content from first 5 posts:\n";
    foreach (array_slice($posts, 0, 5) as $i => $p) {
        $ct = $p['content'] ?? $p['name'] ?? '(empty)';
        echo "[$i] " . substr($ct, 0, 200) . PHP_EOL;
    }
}
