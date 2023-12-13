<?php

$input = getInputForDay();

$maps = [];
$map = [];
$mapFlipped = [];
$flippedLines = [];
foreach ($input as $line) {
    $line = trim($line);
    if($line === 'exit') {
        break;
    }

    if ($line === '') {
        $maps[] = [
            'normal' => $map,
            'flipped' => $flippedLines,
        ];
        $map = [];
        $flippedLines = [];
        continue;
    }

    $map[] = $line;
    foreach (str_split($line) as $index => $char) {
        if (!isset($flippedLines[$index])) {
            $flippedLines[$index] = '';
        }

        $flippedLines[$index] .= $char;
    }
}

$maps[] = [
    'normal' => $map,
    'flipped' => $flippedLines,
];

$total = 0;
foreach ($maps as $index => $map) {
    $mirrorValue = getMirrorValue($map);
    echoIfTest($index . ' ' . $mirrorValue . '<br>');
    $total += $mirrorValue;
}
echo('<b>Part 1</b><br>mirror value: ' . $total);

function getMirrorValue(array $map) {
    $normalMap = $map['normal'];
    $possibleHorizontalFlips = [];
    for ($x = 1, $xMax = strlen($normalMap[0]); $x < $xMax; $x++) {
        if (checkIfMirrored($normalMap[0], $x)) {
            $possibleHorizontalFlips[] = $x;
        }
    }

    if (count($possibleHorizontalFlips) > 0) {
        for ($y = 1, $yMax = count($normalMap); $y < $yMax; $y++) {
            foreach ($possibleHorizontalFlips as $possibleFlip) {
                if (!checkIfMirrored($normalMap[$y], $possibleFlip)) {
                    $index = array_search($possibleFlip, $possibleHorizontalFlips, true);
                    unset($possibleHorizontalFlips[$index]);
                    if (count($possibleHorizontalFlips) === 0) {
                        break 2;
                    }
                }
            }
        }
    }

    if (count($possibleHorizontalFlips) === 1) {
        return $possibleHorizontalFlips[array_key_first($possibleHorizontalFlips)];
    }

    $flippedMap = $map['flipped'];
    $possibleVerticalFlips = [];
    for ($x = 1, $xMax = strlen($flippedMap[0]); $x < $xMax; $x++) {
        if (checkIfMirrored($flippedMap[0], $x)) {
            $possibleVerticalFlips[] = $x;
        }
    }

    if (count($possibleVerticalFlips) > 0) {
        for ($y = 1, $yMax = count($flippedMap); $y < $yMax; $y++) {
            foreach ($possibleVerticalFlips as $possibleFlip) {
                if (!checkIfMirrored($flippedMap[$y], $possibleFlip)) {
                    $index = array_search($possibleFlip, $possibleVerticalFlips, true);
                    unset($possibleVerticalFlips[$index]);
                    if (count($possibleVerticalFlips) === 0) {
                        break 2;
                    }
                }
            }
        }
    }

    if (count($possibleVerticalFlips) === 1) {
        return $possibleVerticalFlips[array_key_first($possibleVerticalFlips)] * 100;
    }

    return 0;
}

function checkIfMirrored(string $line, int $split): bool {
    $left = substr($line, 0, $split);
    $leftReversed = strrev($left);
    $right = substr($line, $split);
    $leftRevShort = substr($leftReversed, 0, strlen($right));

    return str_starts_with($right, $leftRevShort);
}
