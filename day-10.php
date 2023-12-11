<?php

$input = getInputForDay();

$pipes = [
    '|' => ['north' => true, 'east' => false, 'south' => true, 'west' => false],
    '-' => ['north' => false, 'east' => true, 'south' => false, 'west' => true],
    'L' => ['north' => true, 'east' => true, 'south' => false, 'west' => false],
    'J' => ['north' => true, 'east' => false, 'south' => false, 'west' => true],
    '7' => ['north' => false, 'east' => false, 'south' => true, 'west' => true],
    'F' => ['north' => false, 'east' => true, 'south' => true, 'west' => false],
    '.' => ['north' => false, 'east' => false, 'south' => false, 'west' => false],
    'S' => ['north' => true, 'east' => true, 'south' => true, 'west' => true],
];

$startingPos = ['x' => -1, 'y' => -1];

for ($y = 0, $yMax = count($input); $y < $yMax; $y++) {
    for ($x = 0, $xMax = strlen(trim($input[0])); $x < $xMax; $x++) {
        $cell = $input[$y][$x];
        if ($cell === 'S') {
            $startingPos['x'] = $x;
            $startingPos['y'] = $y;
            break 2;
        }
    }
}

$steps = 0;
$loopCoords = [];
foreach (['north', 'east', 'south', 'west'] as $dir) {
    $currentPos = $startingPos;
    $currentDir = $dir;
    $loopCoords = [
        $currentPos
    ];

    $steps = 0;
    while (true) {
        $connected = isConnected($currentPos, $currentDir);

        if (!$connected) {
            // Not connected, no loop, restart
            break;
        }

        $steps++;
        $newPos = getNewPosForDir($currentPos, $currentDir);
        $currentPos = $newPos;
        $currentDir = getNewDir($input[$currentPos['y']][$currentPos['x']], $currentDir);
        if ($newPos === $startingPos) {
            // Loop completed!
            break 2;
        }
        $loopCoords[] = $newPos;
    }
}
echo('<b>Part 1</b><br>');
echo('steps to other side of loop: ' . ($steps / 2) . '<br>');

$minX = 0;
$maxX = strlen(trim($input[0]));
$minY = 0;
$maxY = count($input);

$grid = [];
$insideCells = [];
for ($y = 0; $y < $maxY; $y++) {
    $line1 = '';
    $line2 = '';
    $line3 = '';

    for ($x = $minX; $x < $maxX; $x++) {
        $coord = ['x' => $x, 'y' => $y];
        if (!in_array($coord, $loopCoords, true)) {
            $insideCells[] = $coord;
        }

        $pipe = $input[$coord['y']][$coord['x']];

        switch ($pipe) {
            case '|':
                $line1 .= '.X.';
                $line2 .= '.X.';
                $line3 .= '.X.';
                break;
            case '-':
                $line1 .= '...';
                $line2 .= 'XXX';
                $line3 .= '...';
                break;
            case 'L':
                $line1 .= '.X.';
                $line2 .= '.XX';
                $line3 .= '...';
                break;
            case 'J':
                $line1 .= '.X.';
                $line2 .= 'XX.';
                $line3 .= '...';
                break;
            case '7':
                $line1 .= '...';
                $line2 .= 'XX.';
                $line3 .= '.X.';
                break;
            case 'F':
                $line1 .= '...';
                $line2 .= '.XX';
                $line3 .= '.X.';
                break;
            case '.':
                $line1 .= '...';
                $line2 .= '...';
                $line3 .= '...';
                break;
            case 'S':
                $line1 .= '.X.';
                $line2 .= 'XXX';
                $line3 .= '.X.';
                break;
        }
    }

    $grid[] = str_split($line1);
    $grid[] = str_split($line2);
    $grid[] = str_split($line3);
}

// printTable();
$infectedCells = [['x' => 0, 'y' => 0]];
$outsideCells = [];
try {
    spread();

} catch (Throwable $e) {
    die($e->getMessage());
}
echo('<hr>');

printTable();
// printPipes();

echo('<b>Part 2</b><br>');
echo('Loop cells: ' . count($loopCoords) . '<br>');
echo('Outside cells ' . count($outsideCells) . '<br>');
echo('Inside cells: ' . count($insideCells) . '<br>');

function spread(): void
{
    global $grid, $insideCells, $infectedCells, $outsideCells;

    $maxY = count($grid);
    $maxX = count($grid[0]);

    while (count($infectedCells)) {
        $infectedCell = array_shift($infectedCells);

        if ($grid[$infectedCell['y']][$infectedCell['x']] === 'O') {
            // Already infected!
            continue;
        }
        $grid[$infectedCell['y']][$infectedCell['x']] = 'O';
        $pipeCords = [
            'x' => (int)floor($infectedCell['x'] / 3),
            'y' => (int)floor($infectedCell['y'] / 3),
        ];

        if (!in_array($pipeCords, $outsideCells, true)) {
            $outsideCells[] = $pipeCords;
        }

        if (in_array($pipeCords, $insideCells, true)) {
            $index = array_search($pipeCords, $insideCells, true);
            if ($index !== false) {
                unset($insideCells[$index]);
            }
        }

        for ($y = -1; $y <= 1; $y++) {
            $newY = $infectedCell['y'] + $y;

            if ($newY < 0 || $newY >= $maxY) {
                continue;
            }

            for ($x = -1; $x <= 1; $x++) {
                $newX = $infectedCell['x'] + $x;

                if ($newX < 0 || $newX >= $maxX) {
                    continue;
                }

                if ($y === 0 && $x === 0) {
                    continue;
                }

                $cell = $grid[$newY][$newX];

                if ($cell === '.') {
                    $infectedCells[] = ['x' => $newX, 'y' => $newY];
                }
            }
        }
    }
}

/*
function spread($currentPos): void {
    global $grid, $insideCells;

    $maxY = count($grid);
    $maxX = count($grid[0]);
    if ($grid[$currentPos['y']][$currentPos['x']] === 'O') {
        // Cell already checked
        return;
    }

    $grid[$currentPos['y']][$currentPos['x']] = 'O';
    $pipeCords = [
        'x' => (int) floor($currentPos['x'] / 3),
        'y' => (int) floor($currentPos['y'] / 3),
    ];

    if (in_array($pipeCords, $insideCells, true)) {
        $index = array_search($pipeCords, $insideCells, true);
        if ($index !== false) {
            unset($insideCells[$index]);
        }
    }

    for ($y = -1; $y <= 1; $y++) {
        $newY = $currentPos['y'] + $y;

        if ($newY < 0 || $newY >= $maxY) {
            continue;
        }

        for ($x = -1; $x <= 1; $x++) {
            $newX = $currentPos['x'] + $x;

            if ($newX < 0 || $newX >= $maxX) {
                continue;
            }
            if ($y === 0 && $x === 0) {
                continue;
            }

            $newCell = $grid[$newY][$newX];
            if ($newCell === 'O') {
                continue;
            }
            if ($newCell === 'X') {
                continue;
            }

            spread(['y' => $newY, 'x' => $newX]);
        }
    }
}
*/

function isConnected($currentPos, $dir): bool
{
    global $input, $pipes;

    $currentPipe = $input[$currentPos['y']][$currentPos['x']];
    if (!$pipes[$currentPipe][$dir]) {
        return false;
    }

    $reversedDir = match ($dir) {
        'north' => 'south',
        'east' => 'west',
        'south' => 'north',
        'west' => 'east',
    };

    $newPos = getNewPosForDir($currentPos, $dir);
    $newPipe = $input[$newPos['y']][$newPos['x']];
    if (!$pipes[$newPipe][$reversedDir]) {
        return false;
    }

    return true;
}

function getNewPosForDir($pos, $dir): array
{
    $deltaX = 0;
    $deltaY = 0;

    switch ($dir) {
        case 'north':
            $deltaY = -1;
            break;
        case 'east':
            $deltaX = 1;
            break;
        case 'south':
            $deltaY = 1;
            break;
        case 'west':
            $deltaX = -1;
            break;
    }

    return [
        'x' => $pos['x'] + $deltaX,
        'y' => $pos['y'] + $deltaY,
    ];
}

function getNewDir($pipe, $incomingDir): string
{
    global $pipes;

    $ignoreConnection = match ($incomingDir) {
        'north' => 'south',
        'east' => 'west',
        'south' => 'north',
        'west' => 'east',
    };

    $connections = $pipes[$pipe];
    foreach ($connections as $dir => $connected) {
        if ($dir === $ignoreConnection) {
            continue;
        }

        if (!$connected) {
            continue;
        }

        return $dir;
    }
}

function printTable(): void
{
    global $grid, $insideCells;

    $table = '
<style>
    * {font-size: 6px;}
    table { border-collapse: collapse}
    tr:nth-child(3n) {
    border-bottom: 1px solid black;
    }
    td:nth-child(3n) {
    border-right: 1px solid black;
    }
    .b {
    background-color: blue; color: white;
    }
    .g {
    background-color: darkgreen; color: white;
    }
    .r {
    background-color: red; color: white;
    }
</style><table>';

    foreach ($grid as $i => $iValue) {
        $table .= '<tr>';
        foreach ($iValue as $j => $jValue) {
            $cell = $jValue;

            $class = '';
            switch (true) {
                case in_array(['x' => (int)floor($j / 3), 'y' => (int)floor($i / 3)], $insideCells, true):
                    $class = 'g';
                    break;
                case $cell === 'O':
                    $class = 'b';
                    break;
                case $cell === 'X':
                    $class = 'r';
                    break;
            }


            $html = sprintf(
                '<span class="%s">%s</span>',
                $class,
                $cell,
            );
            $table .= '<td>' . $html . '</td>';
        }
        $table .= '</tr>';
    }
    $table .= '</table>';

    file_put_contents('10_table.html', $table);
}

function printPipes(): void
{
    global $input, $loopCoords, $insideCells;

    echo('<table>');

    for ($y = 0, $maxY = count($input); $y < $maxY; $y++) {
        echo('<tr>');
        for ($x = 0, $maxX = strlen(trim($input[0])); $x < $maxX; $x++) {
            $cell = $input[$y][$x];
            $backgroundColor = '';
            $color = '';

            if (in_array(['x' => $x, 'y' => $y], $loopCoords, true)) {
                $backgroundColor = 'darkgreen';
                $color = 'white';
            }
            if (in_array(['x' => $x, 'y' => $y], $insideCells, true)) {
                $backgroundColor = 'red';
                $color = 'white';
            }

            $html = sprintf(
                '<span style="background-color: %s; color: %s;">%s</span>',
                $backgroundColor,
                $color,
                $cell,
            );

            echo('<td>' . $html . '</td>');
        }
        echo('</tr>');
    }


    echo('</table>');
}
