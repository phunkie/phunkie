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

use BadMethodCallException;
use Phunkie\Types\ImmSet;
use Phunkie\Types\Pair;

/**
 * Traverse operations for ImmSet.
 *
 * Provides filtering and partitioning operations for sets.
 *
 * @template A The element type
 * @mixin \Phunkie\Types\ImmSet
 */
trait ImmSetTraverseOps
{
    abstract public function toArray(): array;

    /**
     * Returns a new set containing only elements that satisfy the predicate.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3, 4, 5);
     * $evens = $set->filter(fn($x) => $x % 2 === 0);  // ImmSet(2, 4)
     * $all = $set->filter(fn($x) => true);             // ImmSet(1, 2, 3, 4, 5)
     * $none = $set->filter(fn($x) => false);           // ImmSet()
     * ```
     *
     * @param callable(A):bool $condition Predicate function
     * @return ImmSet<A> A new set with filtered elements
     */
    public function filter(callable $condition): ImmSet
    {
        $filtered = [];
        foreach ($this->toArray() as $element) {
            if ($condition($element) === true) {
                $filtered[] = $element;
            } elseif ($condition($element) !== false) {
                throw $this->callableMustReturnBoolean($condition($element));
            }
        }
        return ImmSet(...$filtered);
    }

    /**
     * Returns a new set containing only elements that do NOT satisfy the predicate.
     *
     * Equivalent to filter(fn($x) => !$condition($x)).
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3, 4, 5);
     * $odds = $set->filterNot(fn($x) => $x % 2 === 0);  // ImmSet(1, 3, 5)
     * $none = $set->filterNot(fn($x) => true);          // ImmSet()
     * $all = $set->filterNot(fn($x) => false);          // ImmSet(1, 2, 3, 4, 5)
     * ```
     *
     * @param callable(A):bool $condition Predicate function
     * @return ImmSet<A> A new set with elements not matching the predicate
     */
    public function filterNot(callable $condition): ImmSet
    {
        return $this->filter(fn ($x) => !$condition($x));
    }

    /**
     * Partitions the set into two sets based on a predicate.
     *
     * Returns a Pair where:
     * - First element contains elements satisfying the predicate
     * - Second element contains elements not satisfying the predicate
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3, 4, 5);
     * $pair = $set->partition(fn($x) => $x % 2 === 0);
     * // Pair(ImmSet(2, 4), ImmSet(1, 3, 5))
     *
     * list($evens, $odds) = $pair;
     * // $evens = ImmSet(2, 4)
     * // $odds = ImmSet(1, 3, 5)
     * ```
     *
     * @param callable(A):bool $condition Predicate function
     * @return Pair<ImmSet<A>, ImmSet<A>> Pair of (matching, non-matching) sets
     * @throws BadMethodCallException if predicate doesn't return boolean
     */
    public function partition(callable $condition): Pair
    {
        $trues = [];
        $falses = [];

        foreach ($this->toArray() as $element) {
            $result = $condition($element);
            if ($result === true) {
                $trues[] = $element;
            } elseif ($result === false) {
                $falses[] = $element;
            } else {
                throw $this->callableMustReturnBoolean($result);
            }
        }

        return Pair(ImmSet(...$trues), ImmSet(...$falses));
    }

    /**
     * Creates an exception for invalid predicate return types.
     *
     * @param mixed $result The invalid result
     * @return BadMethodCallException
     */
    private function callableMustReturnBoolean($result): BadMethodCallException
    {
        return new BadMethodCallException(sprintf(
            "Predicate must return a boolean, %s returned",
            gettype($result)
        ));
    }
}
