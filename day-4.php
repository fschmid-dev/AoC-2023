<?php

$input = getInputForDay(4);

$cards = [];
$cardCounts = [];

$cardIterator = 1;
foreach ($input as $line) {
    $line = trim($line);
    $line = str_replace('Card ' . $cardIterator . ': ', '', $line);

    $numberLists = explode(' | ', $line);
    $winningNumbers = explode(' ', $numberLists[0]);
    $cardNumbers = explode(' ', $numberLists[1]);

    addCardCount($cardIterator, 1);

    $card = [
        'number' => $cardIterator,
        'winningNumbers' => $winningNumbers,
        'cardNumbers' => $cardNumbers,
        'matchingNumbers' => []
    ];

    $cardPoints = 0;
    foreach ($cardNumbers as $cardNumber) {
        if ($cardNumber === '' || $cardNumber === ' ') {
            continue;
        }

        if (in_array($cardNumber, $winningNumbers, true)) {
            if ($cardPoints === 0) {
                $cardPoints = 1;
            } else {
                $cardPoints *= 2;
            }
            $card['matchingNumbers'][] = $cardNumber;
        }
    }
    $card['points'] = $cardPoints;
    $cards[$cardIterator] = $card;

    $matchingNumbersCount = count($card['matchingNumbers']);
    $currentCardCount = $cardCounts[$cardIterator];
    for ($i = ($cardIterator + 1); $i <= $cardIterator + $matchingNumbersCount; $i++) {
        addCardCount($i, $currentCardCount);
    }

    $cardIterator++;
}

$totalPoints = 0;
foreach ($cards as $card) {
    $totalPoints += $card['points'];
}
echo('Total card points: ' . $totalPoints . '<br>');

$totalCards = 0;
foreach ($cardCounts as $count) {
    $totalCards += $count;
}
echo('Total cards: ' . $totalCards);

function addCardCount($cardNumber, $count): void
{
    global $cardCounts;

    if (!isset($cardCounts[$cardNumber])) {
        $cardCounts[$cardNumber] = 0;
    }

    $cardCounts[$cardNumber] += $count;
}
