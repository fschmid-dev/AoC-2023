<?php

$input = getInputForDay(5);

$steps = ['seed', 'soil', 'fertilizer', 'water', 'light', 'temperature', 'humidity', 'location'];
$seeds = [];
$map = [];

foreach ($steps as $step) {
    $map[$step] = [];
}

$prevFrom = null;
$from = null;
$to = null;

foreach ($input as $line) {
    $line = trim($line);
    if ($line === 'exit') {
        break;
    }

    if ($line === '') {
        if (!$from) {
            continue;
        }

        foreach ($map[$from] as $fromId => $toId) {
            $map[$to][$toId] = $toId;
        }

        continue;
    }

    if (str_starts_with($line, 'seeds: ')) {
        // Get seeds list
        $seedString = substr($line, strlen('seeds: '));
        $seedList = explode(' ', $seedString);
        $part = isset($_GET['part']) ? (int) $_GET['part'] : null;
        if ($part !== 2) {
            foreach ($seedList as $seed) {
                $seed = (int)$seed;
                $seeds[] = $seed;
                $map['seed'][$seed] = $seed;
            }
        } else {
            for ($i = 0, $iMax = count($seedList); $i < $iMax; $i += 2) {
                for ($seed = (int)$seedList[$i], $seedMax = (int)$seedList[$i] + (int)$seedList[$i + 1]; $seed < $seedMax; $seed++) {
                    $seeds[] = $seed;
                    $map['seed'][$seed] = $seed;
                }
            }
            echo(count($seeds));
            die();
        }
        unset($seedString, $seedList);
        continue;
    }

    if (is_numeric($line[0])) {
        // New mapping instructions
        [$destStart, $sourceStart, $range] = explode(' ', $line);
        $destStart = (int)$destStart;
        $sourceStart = (int)$sourceStart;
        $range = (int)$range;

        $sourceMin = $sourceStart;
        $sourceMax = $sourceStart + $range;
        $sources = array_keys($map[$from]);
        foreach ($sources as $source) {
            if ($source < $sourceMin || $source > $sourceMax) {
                continue;
            }

            $diff = $source - $sourceMin;
            $dest = $destStart + $diff;

            $map[$from][$source] = $dest;
        }

        continue;
    }

    if (is_string($line[0])) {
        // Next mapping step
        $line = str_replace(['-to-', ' map:'], ['|', ''], $line);
        $prevFrom = $from;
        [$from, $to] = explode('|', $line);
    }
}

printMapTable();

function printMapTable()
{
    global $seeds, $map, $steps;

    echo('<table border="1">');

    echo('<tr>');
    foreach ($steps as $step) {
        echo('<th>' . $step . '</th>');
    }
    echo('</tr>');

    foreach ($map['seed'] as $seedId => $soilId) {
        echo('<tr>');
        $prevStep = null;
        $prevStepValue = null;
        foreach ($steps as $step) {
            switch ($step) {
                case 'seed':
                    echo('<td>' . $seedId . '</td>');
                    $prevStepValue = $seedId;
                    break;
                default:
                    $value = getMapValue($prevStep, $prevStepValue);
                    echo('<td>' . $value . '</td>');
                    $prevStepValue = $value;
                    break;
            }
            $prevStep = $step;
        }
        echo('</tr>');
    }

    echo("</table>");
}

function getMapValue($step, $value)
{
    global $map;

    if (!isset($map[$step][$value])) {
        $map[$step][$value] = $value;
    }

    return $map[$step][$value];
}

$lowestLocation = PHP_INT_MAX;
foreach ($map['humidity'] as $humidity => $location) {
    $lowestLocation = min($location, $lowestLocation);
}
echo('lowest location possible: ' . $lowestLocation);
