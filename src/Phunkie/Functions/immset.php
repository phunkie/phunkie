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
    use Phunkie\Types\ImmSet;

    /**
     * Functions for working with immutable sets.
     * 
     * This module provides functions for creating and manipulating
     * immutable sets. Core operations include:
     * - Creating sets from various input formats
     * - Adding and removing elements
     * - Checking membership
     * - Set operations (union, intersection, difference)
     * - Converting between sets and arrays
     *
     * Most functions support both ImmSet and PHP arrays, preserving the input type
     * in the output. All functions are curried for functional composition.
     *
     * Example:
     * ```php
     * // Create sets
     * $set1 = ImmSet(1, 2, 3);
     * $set2 = ImmSet(2, 3, 4);
     * 
     * // Modify elements
     * $added = plus(4)($set1);      // ImmSet(1, 2, 3, 4)
     * $removed = minus(2)($set1);   // ImmSet(1, 3)
     * 
     * // Check membership
     * $has = contains(2)($set1);    // true
     * 
     * // Set operations
     * $union = union($set1)($set2);      // ImmSet(1, 2, 3, 4)
     * $common = intersect($set1)($set2);  // ImmSet(2, 3)
     * $unique = diff($set1)($set2);       // ImmSet(1)
     * 
     * // Convert formats
     * $asArray = toArray($set1);    // [1, 2, 3]
     * ```
     */

    /**
     * Creates an immutable set.
     * 
     * Creates an ImmSet containing unique values.
     *
     * Example:
     * ```php
     * ImmSet(1, 2, 3);    // ImmSet(1, 2, 3)
     * ImmSet(1, 1, 2);    // ImmSet(1, 2)
     * ImmSet();           // Empty set
     * ```
     *
     * @template A
     * @param A ...$values Values to store in set
     * @return ImmSet<A> The immutable set
     */
    function ImmSet(...$values)
    {
        return new ImmSet(...$values);
    }
}

namespace Phunkie\Functions\immset {
    use Phunkie\Types\ImmSet;
    use function Phunkie\Functions\currying\applyPartially; 
    
    /**
     * Adds a value to a set.
     * 
     * Returns a new set with the added value.
     * If the value exists, returns the original set.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2);
     * plus(3)($set);        // ImmSet(1, 2, 3)
     * plus(2)($set);        // ImmSet(1, 2)
     * 
     * // Works with arrays too
     * plus(3)([1, 2]);      // [1, 2, 3]
     * ```
     *
     * @template A
     * @param A $value Value to add
     * @return callable(ImmSet<A>|array<A>):ImmSet<A>|array<A> Function expecting set
     */
    const plus = "\\Phunkie\\Functions\\immset\\plus";
    function plus($value)
    {
        return applyPartially([$value], func_get_args(), function($set) use ($value) {
            if (is_array($set)) {
                return array_values(array_unique(array_merge($set, [$value])));
            }
            return $set->plus($value);
        });
    }
    
    /**
     * Removes a value from a set.
     * 
     * Returns a new set without the specified value.
     * If the value doesn't exist, returns the original set.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * minus(2)($set);        // ImmSet(1, 3)
     * minus(4)($set);        // ImmSet(1, 2, 3)
     * 
     * // Works with arrays too
     * minus(2)([1, 2, 3]);   // [1, 3]
     * ```
     *
     * @template A
     * @param A $value Value to remove
     * @return callable(ImmSet<A>|array<A>):ImmSet<A>|array<A> Function expecting set
     */
    const minus = "\\Phunkie\\Functions\\immset\\minus";
    function minus($value)
    {
        return applyPartially([$value], func_get_args(), function($set) use ($value) {
            if (is_array($set)) {
                return array_values(array_diff($set, [$value]));
            }
            return $set->minus($value);
        });
    }
    
    /**
     * Checks if a set contains a value.
     * 
     * Returns true if the set contains the specified value.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * contains(2)($set);        // true
     * contains(4)($set);        // false
     * 
     * // Works with arrays too
     * contains(2)([1, 2, 3]);   // true
     * ```
     *
     * @template A
     * @param A $value Value to check
     * @return callable(ImmSet<A>|array<A>):bool Function expecting set
     */
    const contains = "\\Phunkie\\Functions\\immset\\contains";
    function contains($value)
    {
        return applyPartially([$value], func_get_args(), function($set) use ($value) {
            if (is_array($set)) {
                return in_array($value, $set, true);
            }
            return $set->contains($value);
        });
    }
    
    /**
     * Computes the union of two sets.
     * 
     * Returns a new set containing elements from both sets.
     *
     * Example:
     * ```php
     * $set1 = ImmSet(1, 2);
     * $set2 = ImmSet(2, 3);
     * union($set1)($set2);        // ImmSet(1, 2, 3)
     * 
     * // Works with arrays too
     * union([1, 2])([2, 3]);      // [1, 2, 3]
     * ```
     *
     * @template A
     * @param ImmSet<A>|array<A> $set1 First set
     * @return callable(ImmSet<A>|array<A>):ImmSet<A>|array<A> Function expecting second set
     */
    const union = "\\Phunkie\\Functions\\immset\\union";
    function union($set1)
    {
        return applyPartially([$set1], func_get_args(), function($set2) use ($set1) {
            if (is_array($set1)) {
                return is_array($set2) ? 
                    array_values(array_unique(array_merge($set1, $set2))) :
                    $set2->union(ImmSet(...$set1))->toArray();
            }
            return $set1->union(is_array($set2) ? ImmSet(...$set2) : $set2);
        });
    }
    
    /**
     * Computes the intersection of two sets.
     * 
     * Returns a new set containing only elements present in both sets.
     *
     * Example:
     * ```php
     * $set1 = ImmSet(1, 2, 3);
     * $set2 = ImmSet(2, 3, 4);
     * intersect($set1)($set2);        // ImmSet(2, 3)
     * 
     * // Works with arrays too
     * intersect([1, 2, 3])([2, 3, 4]); // [2, 3]
     * ```
     *
     * @template A
     * @param ImmSet<A>|array<A> $set1 First set
     * @return callable(ImmSet<A>|array<A>):ImmSet<A>|array<A> Function expecting second set
     */
    const intersect = "\\Phunkie\\Functions\\immset\\intersect";
    function intersect($set1)
    {
        return applyPartially([$set1], func_get_args(), function($set2) use ($set1) {
            if (is_array($set1)) {
                return is_array($set2) ? 
                    array_values(array_unique(array_intersect($set1, $set2))) :
                    $set2->intersect(ImmSet(...$set1))->toArray();
            }
            return $set1->intersect(is_array($set2) ? ImmSet(...$set2) : $set2);
        });
    }
    
    /**
     * Computes the difference of two sets.
     * 
     * Returns a new set containing elements from the first set
     * that are not in the second set.
     *
     * Example:
     * ```php
     * $set1 = ImmSet(1, 2, 3);
     * $set2 = ImmSet(2, 3, 4);
     * diff($set1)($set2);        // ImmSet(1)
     * diff($set2)($set1);        // ImmSet(4)
     * 
     * // Works with arrays too
     * diff([1, 2, 3])([2, 3, 4]); // [1]
     * ```
     *
     * @template A
     * @param ImmSet<A>|array<A> $set1 First set
     * @return callable(ImmSet<A>|array<A>):ImmSet<A>|array<A> Function expecting second set
     */
    const diff = "\\Phunkie\\Functions\\immset\\diff";
    function diff($set1)
    {
        return applyPartially([$set1], func_get_args(), function($set2) use ($set1) {
            if (is_array($set1)) {
                return is_array($set2) ? 
                    array_values(array_unique(array_diff($set1, $set2))) :
                    $set2->diff(ImmSet(...$set1))->toArray();
            }
            return $set1->diff(is_array($set2) ? ImmSet(...$set2) : $set2);
        });
    }
    
    /**
     * Creates a set from an array.
     * 
     * Converts an array to an ImmSet, removing duplicates.
     *
     * Example:
     * ```php
     * fromArray([1, 2, 2, 3]);  // ImmSet(1, 2, 3)
     * fromArray([]);            // Empty set
     * ```
     *
     * @template A
     * @param array<A> $array Array to convert
     * @return ImmSet<A> Resulting set
     */
    function fromArray(array $array): ImmSet
    {
        return ImmSet(...array_values(array_unique($array)));
    }
    
    /**
     * Converts a set to an array.
     * 
     * Returns an array containing the set's elements.
     *
     * Example:
     * ```php
     * $set = ImmSet(1, 2, 3);
     * toArray($set);         // [1, 2, 3]
     * 
     * // Works with arrays too (returns copy)
     * toArray([1, 2, 3]);   // [1, 2, 3]
     * ```
     *
     * @template A
     * @param ImmSet<A>|array<A> $set Set to convert
     * @return array<A> Resulting array
     */
    function toArray($set): array
    {
        if (is_array($set)) {
            return array_values(array_unique($set));
        }
        return $set->toArray();
    }
}
