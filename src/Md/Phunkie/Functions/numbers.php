<?php

namespace Md\Phunkie\Functions\numbers;

function even(int $n): bool
{
    return $n % 2 === 0;
}

function odd(int $n): bool
{
    return $n % 2 !== 0;
}

function increment(int $n): int
{
    return $n + 1;
}

function decrement(int $n): int
{
    return $n - 1;
}