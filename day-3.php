<?php

$input = getInputForDay(3);
$rows = count($input);
$cols = strlen(trim($input[0]));

$sumOfPartNumbers = 0;
$gearRatioSum = 0;

for ($y = 0; $y < $rows; $y++) {
    for ($x = 0; $x < $cols; $x++) {
        $cell = $input[$y][$x];
        if (!is_numeric($cell) && $cell !== '*') {
            continue;
        }

        if (is_numeric($cell)) {
            $numberLength = getNumberLength($y, $x);
            $number = (int)substr($input[$y], $x, $numberLength);

            if (isPartNumber($y, $x, $numberLength)) {
                $sumOfPartNumbers += $number;
            }

            $x += ($numberLength - 1);
        }

        if ($cell === '*') {
            $gearNumbers = getGearNumbers($y, $x);
            if (count($gearNumbers) === 2) {
                $gearRatio = $gearNumbers[0]['number'] * $gearNumbers[1]['number'];
                $gearRatioSum += $gearRatio;
            } else if ($gearNumbers > 2) {
                $a = 0;
            }
        }
    }
}

echo('Part number sum: ' . $sumOfPartNumbers . '<br>');
echo('Gear ratio sum: ' . $gearRatioSum);

function getNumberLength(int $row, int $col): int
{
    global $input, $cols;
    $length = 0;

    for ($x = $col; $x < $cols; $x++) {
        $cell = $input[$row][$x];
        if (!is_numeric($cell)) {
            break;
        }
        $length++;
    }

    return $length;
}

function isPartNumber(int $row, int $col, int $length): bool
{
    global $input, $rows, $cols;

    $yMin = $row - 1;
    $yMax = $row + 1;
    $xMin = $col - 1;
    $xMax = $col + $length;

    for ($y = $yMin; $y <= $yMax; $y++) {
        for ($x = $xMin; $x <= $xMax; $x++) {
            if ($y < 0 || $y >= $rows || $x < 0 || $x >= $cols) {
                continue;
            }

            $cell = $input[$y][$x];
            if ($y === $row && $x === $col) {
                $x += ($length - 1);
                continue;
            }

            if ($cell === '.') {
                continue;
            }
            if (is_numeric($cell)) {
                continue;
            }

            return true;
        }
    }

    return false;
}

function getGearNumbers(int $row, int $col): array
{
    global $input, $rows, $cols;
    $gearNumbers = [];

    $yMin = max(0, $row - 1);
    $yMax = min($rows, $row + 1);
    $xMin = max(0, $col - 1);
    $xMax = min($cols, $col + 1);

    for ($y = $yMin; $y <= $yMax; $y++) {
        for ($x = $xMin; $x <= $xMax; $x++) {
            if ($y === $row && $x === $col) {
                continue;
            }
            $cell = $input[$y][$x];
            if (is_numeric($cell)) {
                $startPos = getNumberStartPosition($y, $x);
                $length = getNumberLength($startPos['y'], $startPos['x']);
                $number = (int)substr($input[$startPos['y']], $startPos['x'], $length);

                $gearNumber = [
                    'number' => $number,
                    'length' => $length,
                    'x' => $startPos['x'],
                    'y' => $startPos['y']
                ];

                if (!in_array($gearNumber, $gearNumbers, true)) {
                    $gearNumbers[] = $gearNumber;
                }
            }
        }
    }

    return $gearNumbers;
}

function getNumberStartPosition(int $row, int $col): array
{
    global $input;
    $pos = ['y' => $row, 'x' => $col];

    for ($x = $col; $x >= 0; $x--) {
        $cell = $input[$row][$x];
        if (is_numeric($cell)) {
            $pos['x'] = $x;
        } else {
            break;
        }
    }

    return $pos;
}
