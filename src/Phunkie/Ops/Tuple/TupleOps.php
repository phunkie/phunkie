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

/**
 * Utility operations for Tuple.
 *
 * Provides convenient methods for working with Tuples including
 * accessor helpers, transformations, and query operations.
 *
 * @template T1
 * @template T2
 * @template TN
 * @mixin \Phunkie\Types\Tuple
 */
trait TupleOps
{
    abstract public function toArray(): array;
    abstract public function getArity(): int;

    /**
     * Gets an element by its 1-based index.
     *
     * Provides an alternative to property access that can be used in
     * dynamic contexts. Uses 1-based indexing like the _1, _2 properties.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->get(1);  // "a"
     * $tuple->get(2);  // "b"
     * $tuple->get(3);  // "c"
     * // Same as: $tuple->_1, $tuple->_2, $tuple->_3
     * ```
     *
     * @param int $index 1-based index
     * @return mixed The element at the given index
     * @throws \OutOfBoundsException if index is out of range
     */
    public function get(int $index)
    {
        if ($index < 1 || $index > $this->getArity()) {
            throw new \OutOfBoundsException(
                "Index $index is out of bounds for Tuple of arity " . $this->getArity()
            );
        }
        return $this->{"_" . $index};
    }

    /**
     * Applies a function to all elements, returning a new Tuple.
     *
     * Unlike map which applies to all elements in the Tuple trait,
     * this provides explicit iteration with index information.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3);
     * $tuple->mapIndexed(fn($x, $i) => $x * $i);
     * // Tuple(1, 4, 9) - each element multiplied by its 1-based index
     * ```
     *
     * @param callable(mixed,int):mixed $f Function receiving (element, 1-based-index)
     * @return Tuple A new Tuple with transformed elements
     */
    public function mapIndexed(callable $f)
    {
        $result = [];
        $arr = $this->toArray();
        foreach ($arr as $index => $element) {
            $result[] = $f($element, $index + 1); // 1-based index
        }
        return Tuple(...$result);
    }

    /**
     * Executes a function for each element with its index.
     *
     * Useful for side effects. Does not return a value.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->forEachIndexed(fn($x, $i) => echo "$i: $x\n");
     * // Prints:
     * // 1: a
     * // 2: b
     * // 3: c
     * ```
     *
     * @param callable(mixed,int):void $f Function receiving (element, 1-based-index)
     * @return void
     */
    public function forEachIndexed(callable $f): void
    {
        $arr = $this->toArray();
        foreach ($arr as $index => $element) {
            $f($element, $index + 1); // 1-based index
        }
    }

    /**
     * Checks if all elements satisfy the predicate.
     *
     * Returns true only if the predicate returns true for every element.
     *
     * Example:
     * ```php
     * $tuple = Tuple(2, 4, 6, 8);
     * $tuple->forall(fn($x) => $x % 2 === 0);  // true
     *
     * $tuple = Tuple(2, 3, 4);
     * $tuple->forall(fn($x) => $x % 2 === 0);  // false
     * ```
     *
     * @param callable(mixed):bool $predicate Function to test each element
     * @return bool True if all elements satisfy the predicate
     */
    public function forall(callable $predicate): bool
    {
        foreach ($this->toArray() as $element) {
            if (!$predicate($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if any element satisfies the predicate.
     *
     * Returns true if the predicate returns true for at least one element.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3, 4);
     * $tuple->exists(fn($x) => $x > 3);  // true (4 > 3)
     *
     * $tuple = Tuple(1, 2, 3);
     * $tuple->exists(fn($x) => $x > 5);  // false
     * ```
     *
     * @param callable(mixed):bool $predicate Function to test each element
     * @return bool True if any element satisfies the predicate
     */
    public function exists(callable $predicate): bool
    {
        foreach ($this->toArray() as $element) {
            if ($predicate($element)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the Tuple contains the given value.
     *
     * Uses strict equality (===) for comparison.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", 42, true);
     * $tuple->contains(42);     // true
     * $tuple->contains("42");   // false (strict comparison)
     * $tuple->contains(false);  // false
     * ```
     *
     * @param mixed $value Value to search for
     * @return bool True if the value is found
     */
    public function contains($value): bool
    {
        return in_array($value, $this->toArray(), true);
    }

    /**
     * Finds the first element matching the predicate.
     *
     * Returns Some containing the first matching element, or None if
     * no element matches.
     *
     * Example:
     * ```php
     * $tuple = Tuple(1, 2, 3, 4, 5);
     * $tuple->find(fn($x) => $x > 3);  // Some(4)
     * $tuple->find(fn($x) => $x > 10); // None
     * ```
     *
     * @param callable(mixed):bool $predicate Function to test each element
     * @return \Phunkie\Types\Option Some(element) or None
     */
    public function find(callable $predicate)
    {
        foreach ($this->toArray() as $element) {
            if ($predicate($element)) {
                return Some($element);
            }
        }
        return None();
    }

    /**
     * Returns the head (first element) of the Tuple.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->head();  // "a"
     * // Same as: $tuple->_1
     * ```
     *
     * @return mixed The first element
     */
    public function head()
    {
        return $this->_1;
    }

    /**
     * Returns the last element of the Tuple.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->last();  // "c"
     * // Same as: $tuple->_3
     * ```
     *
     * @return mixed The last element
     */
    public function last()
    {
        $arr = $this->toArray();
        return end($arr);
    }

    /**
     * Returns the Tuple without its first element.
     *
     * If the Tuple has only one element, throws an exception.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->tail();  // Tuple("b", "c")
     * ```
     *
     * @return Tuple A new Tuple without the first element
     * @throws \RuntimeException if Tuple has only one element
     */
    public function tail()
    {
        $arr = $this->toArray();
        if (count($arr) <= 1) {
            throw new \RuntimeException("Cannot get tail of Tuple with one or fewer elements");
        }
        array_shift($arr);
        return Tuple(...$arr);
    }

    /**
     * Returns the Tuple without its last element.
     *
     * If the Tuple has only one element, throws an exception.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c");
     * $tuple->init();  // Tuple("a", "b")
     * ```
     *
     * @return Tuple A new Tuple without the last element
     * @throws \RuntimeException if Tuple has only one element
     */
    public function init()
    {
        $arr = $this->toArray();
        if (count($arr) <= 1) {
            throw new \RuntimeException("Cannot get init of Tuple with one or fewer elements");
        }
        array_pop($arr);
        return Tuple(...$arr);
    }

    /**
     * Takes the first n elements of the Tuple.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c", "d");
     * $tuple->take(2);  // Tuple("a", "b")
     * $tuple->take(0);  // throws exception
     * ```
     *
     * @param int $n Number of elements to take (must be >= 1)
     * @return Tuple A new Tuple with the first n elements
     * @throws \InvalidArgumentException if n < 1 or n > arity
     */
    public function take(int $n)
    {
        if ($n < 1) {
            throw new \InvalidArgumentException("Cannot take less than 1 element from Tuple");
        }
        if ($n > $this->getArity()) {
            throw new \InvalidArgumentException(
                "Cannot take $n elements from Tuple of arity " . $this->getArity()
            );
        }
        $arr = array_slice($this->toArray(), 0, $n);
        return Tuple(...$arr);
    }

    /**
     * Drops the first n elements of the Tuple.
     *
     * Example:
     * ```php
     * $tuple = Tuple("a", "b", "c", "d");
     * $tuple->drop(2);  // Tuple("c", "d")
     * ```
     *
     * @param int $n Number of elements to drop
     * @return Tuple A new Tuple without the first n elements
     * @throws \InvalidArgumentException if n results in empty Tuple
     */
    public function drop(int $n)
    {
        if ($n < 0) {
            throw new \InvalidArgumentException("Cannot drop negative number of elements");
        }
        if ($n >= $this->getArity()) {
            throw new \InvalidArgumentException(
                "Cannot drop $n elements from Tuple of arity " . $this->getArity()
            );
        }
        $arr = array_slice($this->toArray(), $n);
        return Tuple(...$arr);
    }
}
