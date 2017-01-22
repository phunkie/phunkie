<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\numbers;

const even = "\\Phunkie\\Functions\\numbers\\even";
function even(int $n): bool
{
    return $n % 2 === 0;
}

const odd = "\\Phunkie\\Functions\\numbers\\odd";
function odd(int $n): bool
{
    return $n % 2 !== 0;
}

const increment = "\\Phunkie\\Functions\\numbers\\increment";
function increment(int $n): int
{
    return $n + 1;
}

const decrement = "\\Phunkie\\Functions\\numbers\\decrement";
function decrement(int $n): int
{
    return $n - 1;
}