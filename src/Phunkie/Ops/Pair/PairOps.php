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

/**
 * Utility operations for Pair.
 *
 * Provides convenient methods for working with Pairs including swap,
 * merge, and accessor methods.
 *
 * @template T1 The type of the first element
 * @template T2 The type of the second element
 * @mixin \Phunkie\Types\Pair
 */
trait PairOps
{
    /**
     * Swaps the positions of the two elements.
     *
     * Creates a new Pair with the first and second elements exchanged.
     * This is useful for changing the perspective or converting between
     * key-value and value-key representations.
     *
     * Example:
     * ```php
     * $pair = Pair("name", "Alice");
     * $pair->swap();
     * // Pair("Alice", "name")
     * ```
     *
     * Properties:
     * - $pair->swap()->swap() === $pair (involution)
     * - $pair->swap()->_1 === $pair->_2
     * - $pair->swap()->_2 === $pair->_1
     *
     * @return Pair<T2,T1> A new Pair with elements swapped
     */
    public function swap()
    {
        return Pair($this->_2, $this->_1);
    }

    /**
     * Merges both elements using a binary function.
     *
     * Combines the first and second elements into a single value using
     * the provided function. This is useful for reducing a Pair to a
     * single result when both elements are related.
     *
     * Example:
     * ```php
     * $pair = Pair(10, 32);
     * $pair->merge(fn($a, $b) => $a + $b);  // 42
     *
     * $pair = Pair("Hello, ", "World");
     * $pair->merge(fn($a, $b) => $a . $b);  // "Hello, World"
     * ```
     *
     * @template R The result type
     * @param callable(T1,T2):R $f Binary function to merge the elements
     * @return R The merged result
     */
    public function merge(callable $f)
    {
        return $f($this->_1, $this->_2);
    }

    /**
     * Applies a function to both elements, returning a Pair of results.
     *
     * Maps the same function over both elements independently. This is
     * different from bimap which uses different functions for each element.
     *
     * Example:
     * ```php
     * $pair = Pair(3, 4);
     * $pair->mapBoth(fn($x) => $x * $x);
     * // Pair(9, 16)
     * ```
     *
     * @template A The result type for both elements
     * @param callable(T1|T2):A $f Function to apply to both elements
     * @return Pair<A,A> A new Pair with both elements transformed
     */
    public function mapBoth(callable $f)
    {
        return Pair($f($this->_1), $f($this->_2));
    }

    /**
     * Gets the first element.
     *
     * Provides a method-based alternative to property access $_1.
     * Useful in contexts where method references are needed.
     *
     * Example:
     * ```php
     * $pair = Pair("name", 42);
     * $pair->fst();  // "name"
     * // Same as: $pair->_1
     * ```
     *
     * @return T1 The first element
     */
    public function fst()
    {
        return $this->_1;
    }

    /**
     * Gets the second element.
     *
     * Provides a method-based alternative to property access $_2.
     * Useful in contexts where method references are needed.
     * This is equivalent to extract() from Comonad.
     *
     * Example:
     * ```php
     * $pair = Pair("name", 42);
     * $pair->snd();  // 42
     * // Same as: $pair->_2 or $pair->extract()
     * ```
     *
     * @return T2 The second element
     */
    public function snd()
    {
        return $this->_2;
    }

    /**
     * Executes a function with both elements as arguments.
     *
     * Useful for side effects or when you want to work with both
     * elements in a callback without extracting them first.
     *
     * Example:
     * ```php
     * $pair = Pair("user", 42);
     * $pair->forEach(fn($key, $value) => echo "$key: $value\n");
     * // Prints: user: 42
     * ```
     *
     * @param callable(T1,T2):void $f Function to execute with both elements
     * @return void
     */
    public function forEach(callable $f): void
    {
        $f($this->_1, $this->_2);
    }

    /**
     * Checks if both elements satisfy their respective predicates.
     *
     * Returns true only if the first predicate succeeds on the first element
     * AND the second predicate succeeds on the second element.
     *
     * Example:
     * ```php
     * $pair = Pair(10, 20);
     * $pair->forallBoth(
     *     fn($x) => $x > 5,
     *     fn($y) => $y > 15
     * );  // true
     * ```
     *
     * @param callable(T1):bool $p1 Predicate for the first element
     * @param callable(T2):bool $p2 Predicate for the second element
     * @return bool True if both predicates pass
     */
    public function forallBoth(callable $p1, callable $p2): bool
    {
        return $p1($this->_1) && $p2($this->_2);
    }

    /**
     * Checks if either element satisfies its predicate.
     *
     * Returns true if the first predicate succeeds on the first element
     * OR the second predicate succeeds on the second element.
     *
     * Example:
     * ```php
     * $pair = Pair(10, 5);
     * $pair->existsBoth(
     *     fn($x) => $x > 15,
     *     fn($y) => $y > 3
     * );  // true (second predicate passes)
     * ```
     *
     * @param callable(T1):bool $p1 Predicate for the first element
     * @param callable(T2):bool $p2 Predicate for the second element
     * @return bool True if at least one predicate passes
     */
    public function existsBoth(callable $p1, callable $p2): bool
    {
        return $p1($this->_1) || $p2($this->_2);
    }
}
