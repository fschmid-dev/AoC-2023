<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo('<style>body { font-family: monospace; font-size: 16px; }</style>');

function getInputForDay($day = null, $type = 'array'): array|string
{
    if (!$day) {
        $day = $_GET['day'];
    }

    $input = "inputs/$day";
    if (isset($_GET['test'])) {
        $input .= '_test';
    }

    if ($type === 'array') {
        return file($input);
    }

    if ($type === 'string') {
        return file_get_contents($input);
    }
}

function ppr(...$data): void
{
    foreach ($data as $item) {
        echo('<pre>' . print_r($item, true) . '</pre>');
    }
}

function echoIfTest($data): void
{
    global $isTest;
    if ($isTest) {
        echo($data);
    }
}

if (isset($_GET['day']) && $_GET['day'] !== '') {
    $day = $_GET['day'];
    $isTest = $_GET['test'] ?? false;
    echo('<a href="/">back</a>');
    if ($isTest) {
        echo(" | <a href='?day=$day'>Challenge</a>");
    } else {
        echo(" | <a href='?day=$day&test=1'>Test</a>");
    }
    echo(sprintf(
        '<br><b>Day %s%s</b>',
        $day,
        $isTest ? ' (Test)' : ''
    ));
    echo('<br>');
    try {
        $start = microtime(true);
        include "day-$day.php";
        $end = microtime(true);
        echo('<hr>');
        echo(sprintf(
            'start: %s | end %s<br>%s',
            $start,
            $end,
            $end - $start
        ));
    } catch (Throwable $e) {
        ppr($e);
    }
} else {
    ?>
    <h1>Advent of Code</h1>
    <ul>
        <?php
        for ($i = 1; $i <= 24; $i++) {
            echo("<li><a href='?day=$i'>Day $i</a> | <a href='?day=$i&test=1'>Test</a></li>");
        }
        ?>
    </ul>
    <?php
}
