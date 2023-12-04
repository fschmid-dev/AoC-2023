<?php

$part = $_GET['part'] ? (int)$_GET['part'] : 'all';
$input = getInputForDay(1);

$total = 0;

if ($part !== 2) {
    echo('<b>Teil 1</b><br>');
    foreach ($input as $line) {
        $line = trim($line);
        if (!$line) {
            continue;
        }

        echo($line . '<br>');
        $matches = [];
        preg_match_all('/(\d)/', $line, $matches);

        $number = $matches[1][0] . '' . $matches[1][count($matches[1]) - 1];
        echo('number: ' . $number . '<br>');
        $total += (int)$number;
        echo('new total:' . $total . '<br>');
        echo('<br>');
    }
    echo('<b>Total: ' . $total . '</b>');
}

if ($part !== 1) {
    echo('<b>Teil 2</b><br>');
    $total = 0;

    $patternFirst = '/(\d|one|two|three|four|five|six|seven|eight|nine)/';
    $patternLast = '/(\d|eno|owt|eerht|ruof|evif|xis|neves|thgie|enin)/';
    foreach ($input as $line) {
        $line = trim($line);
        if (!$line) {
            break;
        }

        echo($line . "<br>");

        $matchesFirst = [];
        preg_match_all($patternFirst, $line, $matchesFirst);
        $matchesLast = [];
        $lineRev = strrev($line);
        preg_match_all($patternLast, $lineRev, $matchesLast);

        $lineWithHighlight = $line;
        $first = $matchesFirst[1][0];
        $last = strrev($matchesLast[1][0]);

        $posFirst = strpos($line, $first);
        if ($posFirst !== false) {
            $lineWithHighlight = substr_replace($lineWithHighlight, '<span style="color: red">' . $first . '</span>', $posFirst, strlen($first));
        }
        $posLast = strrpos($lineWithHighlight, $last);
        if ($posLast !== false) {
            $lineWithHighlight = substr_replace($lineWithHighlight, '<b style="background-color: lightblue">' . $last . '</b>', $posLast, strlen($last));
        }
        echo($lineWithHighlight . '<br>');

        $numberOne = getDigitForSearchResult($first);
        $numberTwo = getDigitForSearchResult($last);
        $number = (int)($numberOne . $numberTwo);


        echo('number: ' . $number . '<br>');
        $total += $number;
        echo('new total:' . $total . '<br>');
        echo('<br>');
    }
    echo('<b>Total: ' . $total . '</b>');
}

function getDigitForSearchResult($result): int|array
{
    if (is_numeric($result)) {
        return $result;
    }

    return str_replace(
        [
            'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'
        ],
        [
            1, 2, 3, 4, 5, 6, 7, 8, 9
        ],
        $result
    );
}
