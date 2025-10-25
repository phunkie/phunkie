<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Pair;

use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;

/**
 * Foldable operations for Pair.
 *
 * Provides folding operations for reducing Pair values. The folding operations
 * work over the second element of the Pair (the "value"), treating the first
 * element as context that is preserved but not folded.
 *
 * @template T1 The type of the first element (context)
 * @template T2 The type of the second element (value)
 * @mixin \Phunkie\Types\Pair
 */
trait PairFoldableOps
{
    /**
     * Left-associative fold over the second element.
     *
     * Returns a curried function that accepts a binary operation.
     * The fold operates only on the second element of the Pair,
     * applying the function with (initial, value).
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $add = fn($acc, $x) => $acc + $x;
     * ($pair->foldLeft(10))($add);  // 52
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(B,T2):B):B Curried fold function
     */
    public function foldLeft($initial): callable
    {
        return applyPartially([$initial], func_get_args(), fn(callable $f) =>
            $f($initial, $this->_2)
        );
    }

    /**
     * Right-associative fold over the second element.
     *
     * Returns a curried function that accepts a binary operation.
     * The fold operates only on the second element of the Pair,
     * applying the function with (value, initial).
     *
     * For a single-element structure like Pair, this is equivalent to
     * foldLeft but with reversed argument order to the function.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $cons = fn($x, $acc) => [$x, ...$acc];
     * ($pair->foldRight([]))($cons);  // [42]
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(T2,B):B):B Curried fold function
     */
    public function foldRight($initial): callable
    {
        return applyPartially([$initial], func_get_args(), fn(callable $f) =>
            $f($this->_2, $initial)
        );
    }

    /**
     * Maps the second element to a Monoid and returns it.
     *
     * Transforms the second element using the function and returns the result.
     * Since Pair has only one element to fold, this is equivalent to applying
     * the function to the second element.
     *
     * Example:
     * ```php
     * Pair("label", 5)->foldMap(fn($x) => [$x]);  // [5]
     * Pair("label", "hello")->foldMap(fn($x) => $x); // "hello"
     * ```
     *
     * @template B
     * @param callable(T2):B $f Function that maps to a Monoid
     * @return B The mapped value
     */
    public function foldMap(callable $f)
    {
        return $f($this->_2);
    }

    /**
     * Folds the second element using its Monoid instance.
     *
     * Returns a curried function that accepts a binary operation.
     * Equivalent to foldLeft but uses the provided function to combine
     * the initial value with the second element.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * ($pair->fold(10))(fn($a, $b) => $a + $b);  // 52
     * ```
     *
     * @template B
     * @param B $initial Initial value
     * @return callable(callable(B,T2):B):B Curried fold function
     */
    public function fold($initial): callable
    {
        return applyPartially([$initial], func_get_args(), fn(callable $f) =>
            $f($initial, $this->_2)
        );
    }

    /**
     * Converts the Pair to a list containing only the second element.
     *
     * Returns an ImmList with a single element (the second element of the Pair).
     * The first element is not included as it represents context, not a value
     * in the foldable structure.
     *
     * Example:
     * ```php
     * Pair("label", 42)->toList();  // ImmList(42)
     * ```
     *
     * @return \Phunkie\Types\ImmList<T2>
     */
    public function toList()
    {
        return ImmList($this->_2);
    }

    /**
     * Converts the Pair to an array containing only the second element.
     *
     * Returns an array with a single element (the second element of the Pair).
     * This is different from the Pair's toArray() method which returns both elements.
     *
     * Note: This is the Foldable toArray, which treats Pair as containing one
     * foldable value (the second element). Use the Pair's native toArray()
     * method to get both elements.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $pair->toList()->toArray();  // [42] - foldable structure
     * $pair->toArray();            // ["label", 42] - both elements
     * ```
     *
     * @return array<int,T2>
     */
    public function toFoldableArray(): array
    {
        return [$this->_2];
    }
}
