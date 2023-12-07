<?php

$part = isset($_GET['part']) ? (int) $_GET['part'] : null;
$input = getInputForDay(5);

$seedRanges = [];
$steps = [];
$locationsBySeed = [];
$from = null;
$to = null;
$mappings = [];

unset($from, $to, $i, $iMax, $input);

$lowestLocation = PHP_INT_MAX;

$seedCount = 0;
foreach ($seedRanges as $seedRange) {
    $seedCount+=$seedRange['range'];
}

echo('Seed count: ' . $seedCount . '<br>');

echo('lowest location: ' . $lowestLocation);


function getMappingValue($to, $fromId): int {
    global $mappings;

    foreach ($mappings[$to] as $mapping) {
        [$destStart, $sourceStart, $range] = explode(' ', $mapping);
        $destStart = (int)$destStart;
        $sourceStart = (int)$sourceStart;
        $range = (int)$range;

        $sourceMin = $sourceStart;
        $sourceMax = $sourceStart + $range;

        if ($fromId >= $sourceMin && $fromId <= $sourceMax) {
            $diff = $fromId - $sourceMin;
            return $destStart + $diff;
        }
    }

    return $fromId;
}
