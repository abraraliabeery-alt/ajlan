<?php
$geo = json_decode(file_get_contents(__DIR__.'/public/blocks.geojson'), true);
$seeder = file_get_contents(__DIR__.'/database/seeders/PropertySeeder.php');
preg_match_all("/'(T\/\d+)'/", $seeder, $m);
$codes = array_unique($m[1]);

$out = [];
foreach ($geo['features'] as $f) {
    $bn = $f['properties']['block_no'] ?? null;
    $code = 'T/'.$bn;
    if (!in_array($code, $codes, true)) continue;
    $sumLon = $sumLat = $n = 0;
    $walk = function ($coords) use (&$walk, &$sumLon, &$sumLat, &$n) {
        if (is_array($coords[0])) { foreach ($coords as $c) $walk($c); }
        else { $sumLon += $coords[0]; $sumLat += $coords[1]; $n++; }
    };
    $walk($f['geometry']['coordinates']);
    if ($n) $out[$code] = [round($sumLat / $n, 6), round($sumLon / $n, 6)];
}
file_put_contents(__DIR__.'/public/media/plot_locations.json', json_encode($out, JSON_PRETTY_PRINT));
echo count($out), " plots:\n", json_encode($out), "\n";
