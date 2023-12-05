<?php

$part = isset($_GET['part']) ? (int) $_GET['part'] : null;
$input = getInputForDay(5);

$seedRanges = [];
$steps = [];
$locationsBySeed = [];
$from = null;
$to = null;
$mappings = [];

foreach ($input as $line) {
    $line = trim($line);
    if ($line === 'exit') {
        break;
    }

    if ($line === '') {
        continue;
    }

    if (str_starts_with($line, 'seeds: ')) {
        // Get seeds list
        $seedString = substr($line, strlen('seeds: '));
        $seedList = explode(' ', $seedString);
        if ($part !== 2) {
            foreach ($seedList as $seed) {
                $seed = (int)$seed;
                $seedRanges[] = [
                    'start' => $seed,
                    'range' => 1,
                ];
            }
        } else {
            for ($i = 0, $iMax = count($seedList); $i < $iMax; $i += 2) {
                $seedRanges[] = [
                    'start' => (int) $seedList[$i],
                    'range' => (int) $seedList[$i + 1],
                ];
            }
        }
        unset($seedString, $seedList, $seed);
        continue;
    }

    if (is_numeric($line[0])) {
        $mappings[$to][] = $line;
        continue;
    }

    if (is_string($line[0])) {
        // Next mapping step
        $line = str_replace(['-to-', ' map:'], ['|', ''], $line);
        [$from, $to] = explode('|', $line);
        $steps[] = $to;
    }
}

unset($from, $to, $i, $iMax, $input);

$lowestLocation = PHP_INT_MAX;

foreach ($seedRanges as $seedRange) {
    for ($i = $seedRange['start'], $iMax = $seedRange['start'] + $seedRange['range']; $i < $iMax; $i++) {
        if ($isTest) {
            echo('Seed:' . $i . '<br>');
        }
        $value = $i;
        foreach ($steps as $step) {
            $value = getMappingValue($step, $value);
            if ($isTest) {
                echo("$step: " . $value . "<br>");
            }

            if ($step === 'location') {
                $lowestLocation = min($lowestLocation, $value);
            }
        }

        if ($isTest) {
            echo('<br>');
        }
    }
}

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
