<?php

$input = getInputForDay(5);

$seedRanges1 = [];
$seedRanges2 = [];
$steps = [];
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

        for ($i = 0, $iMax = count($seedList); $i < $iMax; $i++) {
            $seed = (int)$seedList[$i];
            $seedRanges1[] = [
                'start' => $seed,
                'range' => 1,
            ];

            if ($i % 2 === 0) {
                $seedRanges2[] = [
                    'start' => $seed,
                    'range' => (int)$seedList[$i + 1],
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

echo('<h2>Part 1</h2>');
$lowestLocation = PHP_INT_MAX;
foreach ($seedRanges1 as $seedRange) {
    $seedMin = $seedRange['start'];
    $seedMax = $seedMin + ($seedRange['range'] - 1);

    $ranges = [[
        'start' => $seedMin,
        'end' => $seedMax
    ]];
    $step = 'seed';
    echoIfTest('<b>' . $step . ' to ');
    foreach ($steps as $step) {
        echoIfTest($step . '</b><br>');
        foreach ($ranges as $range) {
            echoIfTest(sprintf('range %d - %d<br>', $range['start'], $range['end']));
            $ranges = getMappingRange($step, $range);
            foreach ($ranges as $newRange) {
                echoIfTest(sprintf('=> %d - %d<br>', $newRange['start'], $newRange['end']));
            }

            if ($step === 'location') {
                echoIfTest('<b>result</b><br>');
                foreach ($ranges as $locationRange) {
                    echoIfTest(sprintf('range %d - %d<br>', $locationRange['start'], $locationRange['end']));
                    if ($locationRange['start'] < $lowestLocation) {
                        $lowestLocation = $locationRange['start'];
                    }
                }
            }
        }
        if ($step !== 'location') {
            echoIfTest('<b>' . $step . ' to ');
        }
    }
    echoIfTest('<br><hr><br>');
}
echo('lowest location: ' . $lowestLocation);
echo('<hr>');
echo('<h2>Part 2</h2>');

$lowestLocation = PHP_INT_MAX;
foreach ($seedRanges2 as $seedRange) {
    $seedMin = $seedRange['start'];
    $seedMax = $seedMin + ($seedRange['range'] - 1);

    $newRanges = [[
        'start' => $seedMin,
        'end' => $seedMax
    ]];
    $step = 'seed';
    echoIfTest('<b>' . $step . ' to ');
    foreach ($steps as $step) {
        echoIfTest($step . '</b><br>');
        $ranges = $newRanges;
        foreach ($ranges as $range) {
            echoIfTest(sprintf('range %d - %d<br>', $range['start'], $range['end']));
            $newRanges = getMappingRange($step, $range);
            foreach ($newRanges as $newRange) {
                echoIfTest(sprintf('=> %d - %d<br>', $newRange['start'], $newRange['end']));
            }

            if ($step === 'location') {
                echoIfTest('<b>result</b><br>');
                foreach ($newRanges as $locationRange) {
                    echoIfTest(sprintf('range %d - %d<br>', $locationRange['start'], $locationRange['end']));
                    if ($locationRange['start'] < $lowestLocation) {
                        $lowestLocation = $locationRange['start'];
                    }
                }
            }
        }
        if ($step !== 'location') {
            echoIfTest('<b>' . $step . ' to ');
        }
    }
    echoIfTest('<br><hr><br>');
}
echo('lowest location: ' . $lowestLocation);

function getMappingRange(string $to, array $fromRange): array
{
    global $mappings;
    $ranges = [];
    foreach ($mappings[$to] as $mapping) {
        [$destStart, $sourceStart, $range] = explode(' ', $mapping);
        $destStart = (int)$destStart;
        $sourceStart = (int)$sourceStart;
        // A Range of 1 only means the starting number
        // So we need to subtract always 1 for valid calculations
        $range = (int)$range - 1;

        $sourceMin = $sourceStart;
        $sourceMax = $sourceStart + $range;

        if ($fromRange['start'] >= $sourceMin && $fromRange['end'] <= $sourceMax) {
            // Range is completely in mapping
            // Only one range is returned
            // 1. Modified range because of mapping

            $offset = $fromRange['start'] - $sourceMin;
            $fromRangeDiff = $fromRange['end'] - $fromRange['start'];

            $ranges[] = [
                'start' => $destStart + $offset,
                'end' => $destStart + $offset + $fromRangeDiff,
            ];
        } else if ($fromRange['start'] >= $sourceMin && $fromRange['start'] <= $sourceMax) {
            // Range start is in mapping
            // Create two new ranges
            // 1. Modified first, because inside of mapping
            // 2. Unmodified second, because outside of mapping
            $diff = $sourceMax - $fromRange['start'];

            $ranges[] = [
                'start' => $destStart,
                'end' => $destStart + $diff,
            ];
            $ranges[] = [
                'start' => $sourceMax + 1,
                'end' => $fromRange['end']
            ];
        } else if ($fromRange['end'] >= $sourceMin && $fromRange['end'] <= $sourceMax) {
            // Range end is in mapping
            // Create two new ranges
            // 1. Unmodified first, because outside of mapping
            // 2. Modified second, because inside of mapping

            $diff = $fromRange['end'] - $sourceMin;

            $ranges[] = [
                'start' => $fromRange['start'],
                'end' => $sourceMin - 1,
            ];
            $ranges[] = [
                'start' => $destStart,
                'end' => $destStart + $diff,
            ];
        } else if ($sourceMin >= $fromRange['start'] && $sourceMax <= $fromRange['end']) {
            // Whole mapping is inside range
            // Create three ranges
            // 1. Unmodified, because before mapping
            // 2. Modified, because inside mapping
            // 3. Unmodified, because after mapping

            $ranges[] = [
                'start' => $fromRange['start'],
                'end' => $sourceMin - 1,
            ];
            $ranges[] = [
                'start' => $destStart,
                'end' => $destStart + $range,
            ];
            $ranges[] = [
                'start' => $sourceMax + 1,
                'end' => $fromRange['end'],
            ];
        }
    }

    if (count($ranges) < 1) {
        $ranges[] = $fromRange;
    }

    return $ranges;
}
