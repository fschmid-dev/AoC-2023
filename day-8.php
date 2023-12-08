<?php

$input = getInputForDay();

$day = (int)$_GET['day'];
$test = $_GET['test'] ?? false;
$part = isset($_GET['part']) ? (int)$_GET['part'] : 1;
$queryString = sprintf(
    '?day=%d%s',
    $day,
    $test ? ('&test=1') : ''
);

$lrInstructions = null;
$tree = [];
$startingLocations = [];

foreach ($input as $line) {
    $line = trim($line);

    if ($line === '') {
        continue;
    }

    if ($line === 'exit') {
        break;
    }

    if (!$lrInstructions) {
        $lrInstructions = $line;
        continue;
    }

    preg_match_all('/([A-Z1-9]{3})/', $line, $matches);

    $tree[$matches[0][0]] = [
        'L' => $matches[0][1],
        'R' => $matches[0][2],
    ];

    if ($matches[0][0][2] === 'A') {
        $startingLocations[] = $matches[0][0];
    }
}

$lrInstructionsIndex = 0;
$lrInstructionsIndexMax = strlen($lrInstructions);
if ($part !== 2) {
    $steps = 0;
    $location = 'AAA';
    while (true) {
        $instruction = $lrInstructions[$lrInstructionsIndex++];
        if ($lrInstructionsIndex >= $lrInstructionsIndexMax) {
            $lrInstructionsIndex = 0;
        }

        $steps++;
        $location = $tree[$location][$instruction];

        if ($location === 'ZZZ') {
            break;
        }
    }

    echo('Part 1:<br>');
    echo('Needed ' . $steps . ' steps');
} else {
    /*
    $steps = 0;
    $locations = $startingLocations;
    $simultaneousLocations = count($startingLocations);
    while(true) {
        $instruction = $lrInstructions[$lrInstructionsIndex++];
        if ($lrInstructionsIndex >= $lrInstructionsIndexMax) {
            $lrInstructionsIndex = 0;
        }

        $steps++;
        $countTargetsEndingWithZ = 0;
        $locations = array_map(static function ($location) use ($tree, $instruction, &$countTargetsEndingWithZ) {
            $target = $tree[$location][$instruction];

            if (str_ends_with($target, 'Z')) {
                $countTargetsEndingWithZ++;
            }

            return $target;
        }, $locations);

        if ($countTargetsEndingWithZ === $simultaneousLocations) {
            break;
        }
    }
    */

    $stepList = [];
    foreach ($startingLocations as $location) {
        echo('<b>' . $location . '</b><br>');
        $steps = 0;
        while (true) {
            $instruction = $lrInstructions[$lrInstructionsIndex++];
            if ($lrInstructionsIndex >= $lrInstructionsIndexMax) {
                $lrInstructionsIndex = 0;
            }

            $steps++;
            $location = $tree[$location][$instruction];

            if (str_ends_with($location,  'Z')) {
                break;
            }
        }
        echo('steps: ' . $steps . '<br><br>');
        $stepList[] = $steps;
    }


    echo('lcm: ' . my_lcm($stepList));
}

function my_lcm($zahlen) {
    $resultat = array_shift($zahlen);
    foreach ($zahlen as $zahl) {
        $resultat = gmp_lcm($resultat, $zahl);
    }
    return $resultat;
}
