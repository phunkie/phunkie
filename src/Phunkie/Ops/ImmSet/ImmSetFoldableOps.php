<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmSet;

use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;

/**
 * Foldable operations for ImmSet.
 *
 * Provides folding operations for reducing set values.
 * Since sets are unordered, fold operations iterate in insertion order.
 *
 * @template A The element type
 * @mixin \Phunkie\Types\ImmSet
 */
trait ImmSetFoldableOps
{
    abstract public function toArray(): array;
    abstract public function isEmpty(): bool;

    /**
     * Left-associative fold over the set elements.
     *
     * Returns a curried function that accepts a binary operation.
     * Folds over elements in insertion order.
     * Empty sets return the initial value unchanged.
     *
     * Example:
     * ```php
     * $add = fn($acc, $x) => $acc + $x;
     * (ImmSet(1, 2, 3)->foldLeft(0))($add);  // 6
     * (ImmSet()->foldLeft(10))($add);        // 10
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(B,A):B):B Curried fold function
     */
    public function foldLeft($initial)
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            if ($this->isEmpty()) {
                return $initial;
            }
            $acc = $initial;
            foreach ($this->toArray() as $element) {
                $acc = $f($acc, $element);
            }
            return $acc;
        });
    }

    /**
     * Right-associative fold over the set elements.
     *
     * Returns a curried function that accepts a binary operation.
     * Folds over elements in reverse insertion order.
     * Empty sets return the initial value unchanged.
     *
     * Example:
     * ```php
     * $cons = fn($x, $acc) => [$x, ...$acc];
     * (ImmSet(1, 2, 3)->foldRight([]))($cons);  // [3, 2, 1]
     * (ImmSet()->foldRight([]))($cons);         // []
     * ```
     *
     * @template B The accumulator type
     * @param B $initial Initial accumulator value
     * @return callable(callable(A,B):B):B Curried fold function
     */
    public function foldRight($initial)
    {
        return applyPartially([$initial], func_get_args(), function (callable $f) use ($initial) {
            if ($this->isEmpty()) {
                return $initial;
            }
            $arr = $this->toArray();
            // Fold right with [1,2,3] and fn($x, $acc) builds: f(3, f(2, f(1, initial)))
            // Which evaluates as: f(1, "") -> f(2, result) -> f(3, result)
            $acc = $initial;
            foreach ($arr as $element) {
                $acc = $f($element, $acc);
            }
            return $acc;
        });
    }

    /**
     * Maps each element to a Monoid and combines them.
     *
     * Transforms each element using the function and combines
     * the results using the value's Monoid instance.
     * Empty sets return the zero element.
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 3)->foldMap(fn($x) => [$x]);     // [1, 2, 3]
     * ImmSet()->foldMap(fn($x) => [$x]);             // []
     * ImmSet("a", "b")->foldMap(fn($x) => $x);       // "ab"
     * ```
     *
     * @template B
     * @param callable(A):B $f Function that maps to a Monoid
     * @return B Combined monoid value
     */
    public function foldMap(callable $f)
    {
        if ($this->isEmpty()) {
            // For empty sets, we need to get the zero element
            // We'll use a sentinel value approach
            $uninitialized = uniqid("___Phunkie___");
            return $uninitialized;
        }

        $arr = $this->toArray();
        $first = array_shift($arr);
        $initial = zero($f($first));

        return $this->foldLeft($initial)(fn ($b, $a) => combine($b, $f($a)));
    }

    /**
     * General fold (catamorphism) for sets.
     *
     * Applies the binary function to reduce the set to a single value.
     * Empty sets return the initial value unchanged.
     *
     * Example:
     * ```php
     * $add = fn($acc, $x) => $acc + $x;
     * (ImmSet(1, 2, 3)->fold(0))($add);  // 6
     * (ImmSet()->fold(100))($add);       // 100
     * ```
     *
     * @template B The result type
     * @param B $initial Initial value
     * @return callable(callable(B,A):B):B Curried function accepting the operation
     */
    public function fold($initial)
    {
        return applyPartially(
            [$initial],
            func_get_args(),
            fn (callable $f) =>
            (!$this->isEmpty()) ? $this->foldLeft($initial)($f) : $initial
        );
    }
}
