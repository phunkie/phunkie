<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

/**
 * Represents types that can be "folded" into a single value.
 * 
 * Foldable abstracts over data structures that can be reduced to a single value
 * by applying an associative binary operation. It provides multiple ways to
 * traverse and combine elements.
 *
 * Example:
 * ```php
 * $list = ImmList(1, 2, 3, 4);
 * $list->foldLeft(0)(fn($acc, $x) => $acc + $x);  // 10
 * 
 * // Using monoids
 * $strings = ImmList("a", "b", "c");
 * $strings->foldMap(fn($x) => $x);  // "abc"
 * ```
 *
 * @template A
 */
interface Foldable
{
    /**
     * Left-associative fold of a structure.
     *
     * @template B
     * @param B $initial Initial value
     * @return callable(callable(B,A):B):B Function that performs the fold
     */
    public function foldLeft($initial);

    /**
     * Right-associative fold of a structure.
     *
     * @template B
     * @param B $initial Initial value
     * @return callable(callable(B,A):B):B Function that performs the fold
     */
    public function foldRight($initial);

    /**
     * Maps elements to a monoid and combines the results.
     *
     * @template B
     * @param callable(A):B $f Mapping function to a monoid
     * @return B The combined result
     */
    public function foldMap(callable $f);
}
