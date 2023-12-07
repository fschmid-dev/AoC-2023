<?php

$input = getInputForDay(6);

$races = [];
$times = explode(' ', preg_replace('!\s+!', ' ', trim($input[0])));
$distances = explode(' ', preg_replace('!\s+!', ' ', trim($input[1])));

for ($i = 1, $iMax = count($times); $i < $iMax; $i++) {
    $races[] = [
        'time' => (int)$times[$i],
        'distance' => (int)$distances[$i],
    ];
}

echo('Part 1<br>');
$winsProduct = 1;
for ($r = 0, $rMax = count($races); $r < $rMax; $r++) {
    $race = $races[$r];
    $wins = getWinsForRace($race['time'], $race['distance']);
    $winsProduct *= $wins;
}
echo('wins product: ' . $winsProduct . '<br>');

echo('<hr>Part 2<br>');
$time = '';
$distance = '';
foreach ($races as $race) {
    $time .= $race['time'];
    $distance .= $race['distance'];
}
echo('Time: ' . $time . '<br>');
echo('Distance: ' . $distance . '<br>');

$wins = getWinsForRace($time, $distance);
echo('possible wins: ' . $wins . '<br>');

function getWinsForRace(int $time, int $distance)
{
    global $isTest;

    $wins = 0;
    if ($isTest) {
        echo(sprintf(
            '<b>Time %d - Distance %d</b><br>',
            $time,
            $distance
        ));
    }

    $iMax = ($time / 2);
    for ($i = 1; $i < $iMax; $i++) {
        $timeRemaining = $time - $i;
        $distanceTraveled = $i * $timeRemaining;

        if ($isTest) {
            echo(sprintf(
                'Charge for <b>%d</b> ms for a travel of <b>%d</b> mm<br>',
                $i,
                $distanceTraveled
            ));
        }

        if ($distanceTraveled > $distance) {
            $wins = $time - (2 * $i) + 1;

            break;
        }
    }
    if ($isTest) {
        echo('Wins: ' . $wins . '<br>');
    }

    return $wins;
}
