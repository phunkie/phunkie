<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\Pair;

    function Pair(...$args)
    {
        return new Pair(...$args);
    }
}

namespace Phunkie\Functions\pair {

    use Phunkie\Types\Pair;

    const _1 = "\\Phunkie\\Functions\\pair\\_1";
    const _2 = "\\Phunkie\\Functions\\pair\\_2";
    const swap = "\\Phunkie\\Functions\\pair\\swap";
    /**
     * Functions for working with Pairs.
     * 
     * This module provides functions for creating and manipulating
     * pairs of values (tuples of size 2).
     */

    /**
     * Creates a Pair from two values.
     * 
     * Constructs a tuple containing two elements.
     *
     * Example:
     * ```php
     * Pair(1, "a");      // Pair(1, "a")
     * Pair("x", [1,2]);  // Pair("x", [1,2])
     * ```
     *
     * @template A,B
     * @param A $a First value
     * @param B $b Second value
     * @return Pair<A,B> Tuple of two values
     */
    function Pair($a, $b): Pair
    {
        return new Pair($a, $b);
    }

    /**
     * Gets the first element of a pair.
     * 
     * Returns the first (left) value from a pair.
     *
     * Example:
     * ```php
     * $pair = Pair("a", 1);
     * _1($pair);  // "a"
     * ```
     *
     * @template A,B
     * @param Pair<A,B> $pair Pair to get value from
     * @return A First value
     */
    function _1(Pair $pair)
    {
        return $pair->_1;
    }

    /**
     * Gets the second element of a pair.
     * 
     * Returns the second (right) value from a pair.
     *
     * Example:
     * ```php
     * $pair = Pair("a", 1);
     * _2($pair);  // 1
     * ```
     *
     * @template A,B
     * @param Pair<A,B> $pair Pair to get value from
     * @return B Second value
     */
    function _2(Pair $pair)
    {
        return $pair->_2;
    }

    /**
     * Swaps the elements of a pair.
     * 
     * Returns a new pair with first and second elements swapped.
     *
     * Example:
     * ```php
     * $pair = Pair("a", 1);
     * swap($pair);  // Pair(1, "a")
     * ```
     *
     * @template A,B
     * @param Pair<A,B> $pair Pair to swap
     * @return Pair<B,A> Swapped pair
     */
    function swap(Pair $pair): Pair
    {
        return Pair($pair->_2, $pair->_1);
    }
}
