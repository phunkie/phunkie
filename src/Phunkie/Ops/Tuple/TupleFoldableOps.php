<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Tuple;

use function Phunkie\Functions\currying\applyPartially;

/**
 * Foldable operations for Tuple.
 *
 * Provides folding operations for reducing all elements in a Tuple.
 * Unlike Pair which only folds over the second element, Tuple folds
 * over all of its elements.
 *
 * @template T1
 * @template T2
 * @template TN
 * @mixin \Phunkie\Types\Tuple
 */
trait TupleFoldableOps
{
    abstract public function toArray(): array;

    /**
     * Left-associative fold over all elements.
     *
     * Returns a curried function that accepts a binary operation.
     * Processes elements from left to right, threading an accumulator
     * through successive applications of the function.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3, 4);
     * $add = fn($acc, $x) => $acc + $x;
     * ($tuple->foldLeft(0))($add);  // 10
     *
     * $tuple = Tuple("a", "b", "c");
     * $concat = fn($acc, $x) => $acc . $x;
     * ($tuple->foldLeft(""))($concat);  // "abc"
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(B,mixed):B):B Curried fold function
     */
    public function foldLeft($initial): callable
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            $acc = $initial;
            foreach ($this->toArray() as $element) {
                $acc = $f($acc, $element);
            }
            return $acc;
        });
    }

    /**
     * Right-associative fold over all elements.
     *
     * Returns a curried function that accepts a binary operation.
     * Processes elements from right to left, threading an accumulator
     * through successive applications of the function.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3, 4);
     * $cons = fn($x, $acc) => [$x, ...$acc];
     * ($tuple->foldRight([]))($cons);  // [1, 2, 3, 4]
     *
     * $tuple = Tuple("a", "b", "c");
     * $concat = fn($x, $acc) => $x . $acc;
     * ($tuple->foldRight(""))($concat);  // "abc"
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(mixed,B):B):B Curried fold function
     */
    public function foldRight($initial): callable
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            $acc = $initial;
            $elements = array_reverse($this->toArray());
            foreach ($elements as $element) {
                $acc = $f($element, $acc);
            }
            return $acc;
        });
    }

    /**
     * Maps each element to a Monoid and combines them.
     *
     * Transforms each element using the function and combines the results
     * using the value's Monoid instance.
     *
     * Example:
     * ```php
     * Tuple(1, 2, 3)->foldMap(fn($x) => [$x]);
     * // [1, 2, 3]
     *
     * Tuple("hello", " ", "world")->foldMap(fn($x) => $x);
     * // "hello world"
     * ```
     *
     * @template B
     * @param callable(mixed):B $f Function that maps to a Monoid
     * @return B Combined monoid value
     */
    public function foldMap(callable $f)
    {
        $arr = $this->toArray();
        if (empty($arr)) {
            throw new \RuntimeException("Cannot foldMap on empty Tuple");
        }

        $result = $f($arr[0]);
        for ($i = 1; $i < count($arr); $i++) {
            $result = \Phunkie\Functions\semigroup\combine($result, $f($arr[$i]));
        }
        return $result;
    }

    /**
     * Folds all elements using the provided function.
     *
     * Returns a curried function that accepts a binary operation.
     * Equivalent to foldLeft but with a more convenient API for
     * some use cases.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3, 4);
     * ($tuple->fold(0))(fn($a, $b) => $a + $b);  // 10
     * ```
     *
     * @template B
     * @param B $initial Initial value
     * @return callable(callable(B,mixed):B):B Curried fold function
     */
    public function fold($initial): callable
    {
        return $this->foldLeft($initial);
    }

    /**
     * Converts the Tuple to an ImmList.
     *
     * Returns an ImmList containing all elements of the Tuple in order.
     *
     * Example:
     * ```php
     * Tuple(1, 2, 3)->toList();  // ImmList(1, 2, 3)
     * ```
     *
     * @return \Phunkie\Types\ImmList
     */
    public function toImmList()
    {
        return ImmList(...$this->toArray());
    }
}
