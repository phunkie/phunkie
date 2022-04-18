<?php

use Phunkie\Types\ImmList;
use function Phunkie\PatternMatching\Referenced\ListWithTail;
use function Phunkie\PatternMatching\Referenced\ListNoTail;

function pattern_matching_example()
{
    function sum(ImmList $list): int
    {
        $on = pmatch($list);
        switch (true) {
        case $on(Nil): return 0;
        case $on(ListNoTail($x, Nil)): return $x;
        case $on(ListWithTail($x, $xs)): return $x + sum($xs);}
    }

    function fib(int $nth): int
    {
        $on = pmatch($nth);
        switch (true) {
        case $on(0): return 0;
        case $on(1): return 1;
        case $on(_): return fib($nth - 1) + fib($nth - 2);}
    }

    function nextSlot(ImmList $numbers): int
    {
        $on = pmatch($numbers);
        switch (true) {
        case $on(Nil): return 0;
        case $on(ListNoTail($head, Nil)): return $head + 1;
        case $on(ListWithTail($head, $tail)) && $head == $tail->head - 1: return nextSlot($tail);
        case $on(_): return $numbers->head + 1; }
    }

    printLn(sum(ImmList(1, 2, 3)));
    printLn(ImmList(fib(1), fib(2), fib(3), fib(4), fib(5), fib(6), fib(7)));
    printLn(nextSlot(ImmList(1, 0, 4, 5)));
}
