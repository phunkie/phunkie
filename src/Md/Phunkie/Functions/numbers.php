<?php

namespace Md\Phunkie\Functions\numbers;

const even = "\\Md\\Phunkie\\Functions\\numbers\\even";
function even(int $n): bool
{
    return $n % 2 === 0;
}

const odd = "\\Md\\Phunkie\\Functions\\numbers\\odd";
function odd(int $n): bool
{
    return $n % 2 !== 0;
}

const increment = "\\Md\\Phunkie\\Functions\\numbers\\increment";
function increment(int $n): int
{
    return $n + 1;
}

const decrement = "\\Md\\Phunkie\\Functions\\numbers\\decrement";
function decrement(int $n): int
{
    return $n - 1;
}