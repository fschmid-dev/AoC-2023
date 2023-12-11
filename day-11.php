<?php

$input = getInputForDay();

$map = [];
$galaxies = [];
$galaxyPairs = [];
$rowsWithoutGalaxies = [];
$colsWithoutGalaxies = [];

$maxY = count($input);
$maxX = strlen(trim($input[0]));

foreach ($input as $rI => $line) {
    $line = trim($line);

    if (!str_contains($line, '#')) {
        $rowsWithoutGalaxies[] = $rI;
    }
}
for ($x = 0; $x < $maxX; $x++) {
    $galaxyFound = false;
    for ($y = 0; $y < $maxY; $y++) {
        $cell = $input[$y][$x];
        if ($cell === '#') {
            $galaxyFound = true;
            break;
        }
    }
    if (!$galaxyFound) {
        $colsWithoutGalaxies[] = $x;
    }
}

for ($y = 0; $y < $maxY; $y++) {
    $line = [];
    for ($x = 0; $x < $maxX; $x++) {
        $cell = $input[$y][$x];
        $line[] = $cell;
    }

    $map[] = $line;
}

$maxY = count($map);
$maxX = count($map[0]);
for ($y = 0; $y < $maxY; $y++) {
    for ($x = 0; $x < $maxX; $x++) {
        $cell = $map[$y][$x];
        if ($cell === '#') {
            $number = count($galaxies) + 1;
            $galaxies[$number] = [
                'y' => $y,
                'x' => $x,
            ];
        }
    }
}

echo('<b>Part 1</b><br>');
createPairs();
$sumOfShortestPaths = 0;
foreach ($galaxyPairs as $galaxyPair) {
    $sumOfShortestPaths += $galaxyPair['dist'];
}
echo('sum of shortest paths: ' . $sumOfShortestPaths);
echo('<hr>');
echo('<b>Part 2</b><br>');
$galaxyPairs = [];
createPairs(1000000 - 1);
$sumOfShortestPaths = 0;
foreach ($galaxyPairs as $galaxyPair) {
    $sumOfShortestPaths += $galaxyPair['dist'];
}
echo('sum of shortest paths: ' . $sumOfShortestPaths);

function createPairs(int $emptyDistance = 1): void
{
    global $galaxies, $galaxyPairs, $rowsWithoutGalaxies, $colsWithoutGalaxies;
    $gCount = count($galaxies);

    for ($a = 1, $aMax = $gCount - 1; $a <= $aMax; $a++) {
        for ($b = $a + 1; $b <= $gCount; $b++) {

            $g1 = $galaxies[$a];
            $g2 = $galaxies[$b];

            $x1 = min($g1['x'], $g2['x']);
            $x2 = max($g1['x'], $g2['x']);
            $y1 = min($g1['y'], $g2['y']);
            $y2 = max($g1['y'], $g2['y']);

            $dist = $x2 - $x1 + $y2 - $y1;

            foreach ($rowsWithoutGalaxies as $row) {
                if ($row > $y1 && $row < $y2) {
                    $dist += $emptyDistance;
                }
            }

            foreach ($colsWithoutGalaxies as $col) {
                if ($col > $x1 && $col < $x2) {
                    $dist += $emptyDistance;
                }
            }

            echoIfTest(sprintf(
                '%d (%d / %d) => %d (%d / %d) | %d<br>',
                $a,
                $g1['x'],
                $g1['y'],
                $b,
                $g2['x'],
                $g2['y'],
                $dist
            ));

            $galaxyPairs[] = [
                0 => $g1,
                1 => $g2,
                'dist' => $dist,
            ];
        }
    }
}

function printMap(): void
{
    global $map, $maxY, $maxX;

    for ($y = 0; $y < $maxY; $y++) {
        for ($x = 0; $x < $maxX; $x++) {
            $cell = $map[$y][$x];
            echo($cell);
        }
        echo('<br>');
    }
}
