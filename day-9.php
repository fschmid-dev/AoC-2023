<?php

$input = getInputForDay();
$readings = [];

foreach ($input as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    if ($line === 'exit') {
        break;
    }

    $numbers = [
        0 => [],
    ];
    foreach (explode(' ', $line) as $number) {
        $numbers[0][] = (int) $number;
    }
    $readings[] = $numbers;
}

for ($s = 0, $sMax = count($readings); $s < $sMax; $s++) {
    $checkSequenz = true;

    $sequenz = $readings[$s];
    $line = 0;
    while ($checkSequenz) {
        $allZero = true;
        $sequenz[$line + 1] = [];
        for ($i = 0, $iMax = count($sequenz[$line]) - 1; $i < $iMax; $i++) {
            $diff = $sequenz[$line][$i + 1] - $sequenz[$line][$i];
            $sequenz[$line + 1][] = $diff;
            if ($diff !== 0) {
                $allZero = false;
            }
        }

        if ($allZero) {
            $checkSequenz = false;
        }

        $line++;
    }

    $lStart = count($sequenz) - 1;
    for ($l = $lStart; $l >= 0; $l--) {
        $numbers = $sequenz[$l];
        if ($l === $lStart) {
            $numbers[] = 0;

            array_unshift($numbers, 0);
        } else {
            // Add next history
            $index = count($sequenz[$l + 1]) - 1;
            $increment = $sequenz[$l + 1][$index];
            $numberA = $sequenz[$l][$index - 1];
            $sequenz[$l][] = $numberA + $increment;

            // Find previous history
            $decrementPrevious = $sequenz[$l + 1][0];
            $numberB = $sequenz[$l][0];
            array_unshift($sequenz[$l], $numberB - $decrementPrevious);
        }
    }

    $readings[$s] = $sequenz;
}

$sumOfNewSequenzNumbers = 0;
$sumOfPreviousHistory = 0;
foreach ($readings as $sequenz) {
    $sumOfNewSequenzNumbers += $sequenz[0][count($sequenz[0]) - 1];
    $sumOfPreviousHistory += $sequenz[0][0];
}

echo('<b>Part 1</b><br>');
echo('sum of new sequenz numbers: ' . $sumOfNewSequenzNumbers . '<br>');
echo('<b>Part 2</b><br>');
echo('sum of prev history numbers: ' . $sumOfPreviousHistory);
