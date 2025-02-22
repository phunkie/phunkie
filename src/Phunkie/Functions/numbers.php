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

use Phunkie\Types\Option;

/**
 * Functions for working with numbers.
 * 
 * This module provides utility functions for working with numbers
 * in a functional style. Includes functions for:
 * - Number predicates (even, odd)
 * - Arithmetic operations
 * - Safe parsing with Option
 */

/**
 * Checks if a number is even.
 * 
 * Returns true if the number is divisible by 2.
 *
 * Example:
 * ```php
 * even(2);  // true
 * even(3);  // false
 * even(0);  // true
 * ```
 *
 * @param int $n Number to check
 * @return bool True if even
 */
const even = "\\Phunkie\\Functions\\numbers\\even";
function even(int $n): bool
{
    return $n % 2 === 0;
}

/**
 * Checks if a number is odd.
 * 
 * Returns true if the number is not divisible by 2.
 *
 * Example:
 * ```php
 * odd(3);  // true
 * odd(2);  // false
 * odd(0);  // false
 * ```
 *
 * @param int $n Number to check
 * @return bool True if odd
 */
const odd = "\\Phunkie\\Functions\\numbers\\odd";
function odd(int $n): bool
{
    return $n % 2 !== 0;
}

/**
 * Increments a number by one.
 * 
 * Returns the number plus one.
 *
 * Example:
 * ```php
 * increment(41);  // 42
 * increment(-1);  // 0
 * ```
 *
 * @param int $n Number to increment
 * @return int Result
 */
const increment = "\\Phunkie\\Functions\\numbers\\increment";
function increment(int $n): int
{
    return $n + 1;
}

/**
 * Decrements a number by one.
 * 
 * Returns the number minus one.
 *
 * Example:
 * ```php
 * decrement(42);  // 41
 * decrement(0);   // -1
 * ```
 *
 * @param int $n Number to decrement
 * @return int Result
 */
const decrement = "\\Phunkie\\Functions\\numbers\\decrement";
function decrement(int $n): int
{
    return $n - 1;
}

/**
 * Negates a number.
 * 
 * Returns the additive inverse of the number.
 *
 * Example:
 * ```php
 * negate(42);   // -42
 * negate(-42);  // 42
 * negate(0);    // 0
 * ```
 *
 * @param int $n Number to negate
 * @return int Negated result
 */
const negate = "\\Phunkie\\Functions\\numbers\\negate";
function negate(int $n): int
{
    return $n * -1;
}

/**
 * Gets the sign of a number.
 * 
 * Returns:
 *  1 for positive numbers
 * -1 for negative numbers
 *  0 for zero
 *
 * Example:
 * ```php
 * signum(42);   // 1
 * signum(-42);  // -1
 * signum(0);    // 0
 * ```
 *
 * @param int $n Number to check
 * @return int Sign (-1, 0, or 1)
 */
const signum = "\\Phunkie\\Functions\\numbers\\signum";
function signum(int $n): int
{
    return $n > 0 ? 1 : ($n < 0 ? -1 : 0);
}

/**
 * Safely divides two numbers.
 * 
 * Returns Some containing the result if divisor is not zero,
 * or None if division by zero would occur.
 *
 * Example:
 * ```php
 * safeDivide(6, 2);   // Some(3)
 * safeDivide(6, 0);   // None
 * ```
 *
 * @param int|float $a Dividend
 * @param int|float $b Divisor
 * @return Option<int|float> Optional result
 */
const safeDivide = "\\Phunkie\\Functions\\numbers\\safeDivide";
function safeDivide($a, $b): Option
{
    return $b == 0 ? None() : Some($a / $b);
}

/**
 * Safely converts a string to a number.
 * 
 * Returns Some containing the number if conversion succeeds,
 * or None if the string is not a valid number.
 *
 * Example:
 * ```php
 * parseNumber("123");    // Some(123)
 * parseNumber("12.34");  // Some(12.34)
 * parseNumber("abc");    // None
 * ```
 *
 * @param string $s String to parse
 * @return Option<int|float> Optional number
 */
const parseNumber = "\\Phunkie\\Functions\\numbers\\parseNumber";
function parseNumber(string $s): Option
{
    if (is_numeric($s)) {
        $num = strpos($s, '.') !== false ? (float)$s : (int)$s;
        return Some($num);
    }
    return None();
}
