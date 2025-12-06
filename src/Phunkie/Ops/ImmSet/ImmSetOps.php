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

use Error;
use Phunkie\Types\ImmList;
use Phunkie\Types\ImmSet;
use Phunkie\Types\Option;

/**
 * Additional set-specific operations for ImmSet.
 *
 * Provides utility methods for common set operations.
 *
 * @template A The element type
 * @mixin \Phunkie\Types\ImmSet
 */
trait ImmSetOps
{
    abstract public function toArray(): array;
    abstract public function isEmpty(): bool;
    abstract public function plus($element): ImmSet;
    abstract public function minus($element): ImmSet;

    /**
     * Alias for plus() - adds an element to the set.
     *
     * Returns a new set with the element added.
     * If the element already exists, returns an identical set.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * $set->add(4);  // ImmSet(1, 2, 3, 4)
     * $set->add(2);  // ImmSet(1, 2, 3)
     * ```
     *
     * @param A $element The element to add
     * @return ImmSet<A> A new set with the element added
     */
    public function add($element): ImmSet
    {
        return $this->plus($element);
    }

    /**
     * Alias for minus() - removes an element from the set.
     *
     * Returns a new set with the element removed.
     * If the element doesn't exist, returns an identical set.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * $set->remove(2);  // ImmSet(1, 3)
     * $set->remove(5);  // ImmSet(1, 2, 3)
     * ```
     *
     * @param A $element The element to remove
     * @return ImmSet<A> A new set without the element
     */
    public function remove($element): ImmSet
    {
        return $this->minus($element);
    }

    /**
     * Checks if this set is a subset of another set.
     *
     * Returns true if all elements of this set are contained in the other set.
     * An empty set is a subset of any set.
     * A set is always a subset of itself.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(1, 2, 3, 4);
     * $s1->subsetOf($s2);  // true
     * $s2->subsetOf($s1);  // false
     * $s1->subsetOf($s1);  // true
     * ImmSet()->subsetOf($s1);  // true
     * ```
     *
     * @param ImmSet<A> $other The set to check against
     * @return bool True if this is a subset of the other set
     */
    public function subsetOf(ImmSet $other): bool
    {
        foreach ($this->toArray() as $element) {
            if (!$other->contains($element)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the minimum element in the set.
     *
     * Example:
     * ```php
     * ImmSet(3, 1, 4, 1, 5)->min();  // Some(1)
     * ImmSet("c", "a", "b")->min();  // Some("a")
     * ImmSet()->min();                // None
     * ```
     *
     * @return Option<A> Some(minimum) or None if empty
     */
    public function min(): Option
    {
        if ($this->isEmpty()) {
            return None();
        }
        return Some(min($this->toArray()));
    }

    /**
     * Returns the maximum element in the set.
     *
     * Example:
     * ```php
     * ImmSet(3, 1, 4, 1, 5)->max();  // Some(5)
     * ImmSet("c", "a", "b")->max();  // Some("c")
     * ImmSet()->max();                // None
     * ```
     *
     * @return Option<A> Some(maximum) or None if empty
     */
    public function max(): Option
    {
        if ($this->isEmpty()) {
            return None();
        }
        return Some(max($this->toArray()));
    }

    /**
     * Converts the set to an ImmList.
     *
     * The resulting list preserves insertion order.
     * Note: Converting to a list and back to a set maintains uniqueness.
     *
     * Example:
     * ```php
     * $set = ImmSet(3, 1, 4);
     * $list = $set->toList();  // ImmList(3, 1, 4)
     * $list->toArray();         // [3, 1, 4]
     * ```
     *
     * @return ImmList<A> A list containing the set elements
     */
    public function toList(): ImmList
    {
        return ImmList(...$this->toArray());
    }

    /**
     * Returns the number of elements in the set (cardinality).
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 3)->size();  // 3
     * ImmSet()->size();          // 0
     * ```
     *
     * @return int The number of elements
     */
    public function size(): int
    {
        return count($this->toArray());
    }

    /**
     * Returns the number of elements in the set (cardinality).
     *
     * Alias for size().
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 3)->length();  // 3
     * ImmSet()->length();          // 0
     * ```
     *
     * @return int The number of elements
     */
    public function length(): int
    {
        return $this->size();
    }

    /**
     * Checks if this set is a proper subset of another set.
     *
     * Returns true if this set is a subset and is not equal to the other set.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(1, 2, 3);
     * $s1->properSubsetOf($s2);  // true
     * $s1->properSubsetOf($s1);  // false
     * ```
     *
     * @param ImmSet<A> $other The set to check against
     * @return bool True if this is a proper subset
     */
    public function properSubsetOf(ImmSet $other): bool
    {
        return $this->subsetOf($other) && $this->size() < $other->size();
    }

    /**
     * Checks if this set is a superset of another set.
     *
     * Returns true if all elements of the other set are contained in this set.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2, 3, 4);
     * $s2 = ImmSet(1, 2);
     * $s1->supersetOf($s2);  // true
     * $s2->supersetOf($s1);  // false
     * ```
     *
     * @param ImmSet<A> $other The set to check against
     * @return bool True if this is a superset of the other set
     */
    public function supersetOf(ImmSet $other): bool
    {
        return $other->subsetOf($this);
    }

    /**
     * Checks if this set is a proper superset of another set.
     *
     * Returns true if this set is a superset and is not equal to the other set.
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2, 3);
     * $s2 = ImmSet(1, 2);
     * $s1->properSupersetOf($s2);  // true
     * $s1->properSupersetOf($s1);  // false
     * ```
     *
     * @param ImmSet<A> $other The set to check against
     * @return bool True if this is a proper superset
     */
    public function properSupersetOf(ImmSet $other): bool
    {
        return $this->supersetOf($other) && $this->size() > $other->size();
    }

    /**
     * Returns the first element of the set.
     *
     * Since sets are unordered, this returns the first element in insertion order.
     * For empty sets, returns None.
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 3)->head();  // Some(1)
     * ImmSet()->head();          // None
     * ```
     *
     * @return Option<A> Some(first element) or None if empty
     */
    public function head(): Option
    {
        if ($this->isEmpty()) {
            return None();
        }
        $arr = $this->toArray();
        return Some($arr[0]);
    }

    /**
     * Checks if two sets are disjoint (have no common elements).
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(3, 4);
     * $s3 = ImmSet(2, 3);
     * $s1->disjoint($s2);  // true
     * $s1->disjoint($s3);  // false
     * ```
     *
     * @param ImmSet<A> $other The set to check against
     * @return bool True if sets have no common elements
     */
    public function disjoint(ImmSet $other): bool
    {
        foreach ($this->toArray() as $element) {
            if ($other->contains($element)) {
                return false;
            }
        }
        return true;
    }
}
