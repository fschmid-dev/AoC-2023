<?php

$input = getInputForDay();

$hands = [];
$handsCount = 0;

$types = [
    0 => 'High card',
    1 => 'One pair',
    2 => 'Two pairs',
    3 => 'Three of a kind',
    4 => 'Full house',
    5 => 'Four of a kind',
    6 => 'Five of a kind',
];

foreach ($input as $line) {
    $line = trim($line);
    if ($line === '') {
        return;
    }

    $parts = explode(' ', $line);

    $hand = [
        'hand' => $parts[0],
        'bid' => (int)$parts[1],
    ];

    foreach (array_count_values(str_split($hand['hand'])) as $char => $count) {
        $hand['cards'][$char] = $count;
    }
    $hand['type'] = getHandType($hand['cards']);
    $hand['type2'] = getHandType2($hand['cards'], $hand['hand']);

    $hands[] = $hand;
}
$handsCount = count($hands);

echo('Part 1<br>');

$sortedHands = $hands;
usort($sortedHands, 'compareHands');

$totalWinnings = 0;
for ($i = 0; $i < $handsCount; $i++) {
    $hand = $sortedHands[$i];
    $rank = $i + 1;
    echoIfTest($hand['bid'] . ' * ' . $rank . '<br>');
    $totalWinnings += $rank * $hand['bid'];
}
echo('Total winnings: ' . $totalWinnings . '<br>');
echo('<hr>');
echo('Part 2<br>');

$sortedHands = $hands;
usort($sortedHands, 'compareHands2');

$totalWinnings = 0;
for ($i = 0; $i < $handsCount; $i++) {
    $hand = $sortedHands[$i];

    $rank = $i + 1;
    echoIfTest('(' . $hand['hand'] . ') ' . $hand['bid'] . ' * ' . $rank . '<br>');
    $totalWinnings += $rank * $hand['bid'];
}
echo('Total winnings: ' . $totalWinnings . '<br>');

function getHandType(array $cards): int
{
    // Order:
    // Five of a kind => 6
    // Four of a kind => 5
    // Full house (3x Y, 2x Z) => 4
    // Three of a kind => 3
    // Two pair => 2
    // One pair => 1
    // High card => 0

    $cardCounts = array_count_values($cards);

    // Check with array_key_exists which card counts are available
    return match (true) {
        // 5 means, there is 5 times one card
        array_key_exists(5, $cardCounts) => 6,
        // 4 means, one card exists four times and we have a single additional
        array_key_exists(4, $cardCounts) => 5,
        // 3 means, we either have a full house (if there exists a card two times) or only three of a kind
        array_key_exists(3, $cardCounts) => array_key_exists(2, $cardCounts) ? 4 : 3,
        // 2 means, we have either two pairs or only one pair
        //  it can't be a full house, as this would already be caught above
        //  if there are only three different cards passed, there must be two pairs (2x 2 + 1x 1)
        //  otherwise its just one pair
        array_key_exists(2, $cardCounts) => count($cards) === 3 ? 2 : 1,
        // nothing caught, let's hope for a good high card
        default => 0,
    };
}

function compareHands(array $handA, array $handB): int {
    $compareTypes = $handA['type'] <=> $handB['type'];

    // Compare types
    if ($compareTypes !== 0) {
        return $compareTypes;
    }

    // If types match, check all five card from left to right for highest card
    for ($i = 0; $i < 5; $i++) {
        $cardA = getCardValue($handA['hand'][$i]);
        $cardB = getCardValue($handB['hand'][$i]);

        $compareCards = $cardA <=> $cardB;
        if ($compareCards !== 0) {
            return $compareCards;
        }
    }

    return 0;
}

function compareHands2(array $handA, array $handB): int {
    $compareTypes = $handA['type2'] <=> $handB['type2'];

    // Compare types
    if ($compareTypes !== 0) {
        return $compareTypes;
    }

    // If types match, check all five card from left to right for highest card
    for ($i = 0; $i < 5; $i++) {
        $cardA = getCardValue($handA['hand'][$i], true);
        $cardB = getCardValue($handB['hand'][$i], true);

        $compareCards = $cardA <=> $cardB;
        if ($compareCards !== 0) {
            return $compareCards;
        }
    }

    return 0;
}

function getCardValue(string|int $card, bool $part2 = false): int
{
    if (is_numeric($card)) {
        return (int) $card;
    }

    return match ($card) {
        'T' => 10,
        // Jokers are the weakest card in part 2
        'J' => $part2 ? -1 : 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    };
}

function getHandType2(array $cards, string $hand): int {
    // Order:
    // Five of a kind => 6
    // Four of a kind => 5
    // Full house (3x Y, 2x Z) => 4
    // Three of a kind => 3
    // Two pair => 2
    // One pair => 1
    // High card => 0
    // Jokers (J) can now mimic other cards (KKJKJ => 5 of a kind, etc)

    $jokerCount = $cards['J'] ?? 0;
    unset($cards['J']);
    $cardCounts = array_count_values($cards);

    // Check for five of a kind
    //  either we have 5 as normal
    //  or X, where X is 5 - jokers
    if (array_key_exists(5 - $jokerCount, $cardCounts) || $jokerCount === 5) {
        return 6;
    }

    // Check for four of a kind
    //  either we have 4 as normal
    //  or X, where X is 4 - jokers
    if (array_key_exists(4 - $jokerCount, $cardCounts)) {
        return 5;
    }

    // Check for full house
    //  either we have 3 as normals and 2 other normals
    //  or (normals + joker) = 3 and 2 other normals
    //  or normals = 3, a normal and a joker
    // something like 'AAJJJ' will already be caught above
    if (array_key_exists(3, $cardCounts) && array_key_exists(2, $cardCounts)) {
        // 3 normals of one kind, and 2 normals of another kind
        // normal full house
        return 4;
    }
    if (array_key_exists(3, $cardCounts)) {
        // We have 3 normal cards, let's check if we have two normal cards remaining or a normal card and a joker
        if (array_key_exists(2 - $jokerCount, $cardCounts)) {
            return 4;
        }
    }
    // Check for (normal card + joker) == 3 + 2 normal of a kind
    if (in_array(3 - $jokerCount, $cards, true)) {
        $card = array_search(3 - $jokerCount, $cards, true);
        $remainingCards = $cards;
        unset($remainingCards['J'], $remainingCards[$card]); // Remove jokers and three-of-a-kind-card
        $remainingCardsCount = array_count_values($remainingCards);

        if (array_key_exists(2, $remainingCardsCount)) {
            // Another normal card exists 2 times,
            //  so it's a full house
            return 4;
        }
    }

    // Check for three of a kind
    //  either 3 normals or
    //  (normal + joker) == 3
    // full houses will already be caught at this point
    // could be moved inside the check above, but let's keep it separate for clarity
    if (array_key_exists(3 - $jokerCount, $cardCounts)) {
        return 3;
    }

    // Check for two pairs
    // only two normal pairs a possible
    //  as soon we have a pair and more than one joker, it's at least a three of a kind
    if (array_key_exists(2, $cardCounts) && count($cards) === 3) {
        return 2;
    }

    // Check for a single pair
    //  either we have it directly or we have one joker card
    if (array_key_exists(2, $cardCounts) || $jokerCount === 1) {
        return 1;
    }

    return 0;
}
