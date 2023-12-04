<?php

$input = getInputForDay(2);

$sumOfValidGames = 0;
$powerOfGames = 0;

$numberOfCubes = [
    'red' => 12,
    'green' => 13,
    'blue' => 14
];

$game = 1;
foreach ($input as $line) {
    $line = trim($line);

    $line = str_replace('Game ' . $game . ': ', '', $line);
    $gamePossible = true;

    $draws = explode('; ', $line);

    $minCubesInGame = [
        'red' => 0,
        'green' => 0,
        'blue' => 0,
    ];

    foreach ($draws as $draw) {
        $cubes = explode(', ', $draw);

        foreach ($cubes as $cube) {
            [$amount, $color] = explode(' ', $cube);
            $amount = (int)$amount;

            // Check for part 1
            if ($numberOfCubes[$color] < $amount) {
                $gamePossible = false;
            }

            // Check for part 2
            $minCubesInGame[$color] = max($minCubesInGame[$color], $amount);
        }
    }

    // Count for part 1
    if ($gamePossible) {
        $sumOfValidGames += $game;
    }

    // Calculate power for part 2
    $power = $minCubesInGame['red'] * $minCubesInGame['blue'] * $minCubesInGame['green'];
    $powerOfGames += $power;

    $game++;
}

echo('Sum of valid games: ' . $sumOfValidGames . '<br>');
echo('Power of all games: ' . $powerOfGames);
