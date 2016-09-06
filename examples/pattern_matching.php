<?php

use Md\Phunkie\Types\ImmList;
use function Md\Phunkie\PatternMatching\Referenced\ListWithTail;
use function Md\Phunkie\PatternMatching\Referenced\ListNoTail;

function pattern_matching_example()
{
    function sum(ImmList $list): int { $on = match($list); switch(true) {
        case $on(Nil): return 0;
        case $on(ListNoTail($x, Nil)): return $x;
        case $on(ListWithTail($x, $xs)): return $x + sum($xs);}
    }

    function fib(int $nth): int { $on = match($nth); switch(true) {
        case $on(0): return 0;
        case $on(1): return 1;
        case $on(_): return fib($nth - 1) + fib($nth - 2);}
    }

    printLn(sum(ImmList(1,2,3)));
    printLn(ImmList(fib(1), fib(2), fib(3), fib(4), fib(5), fib(6), fib(7)));
}