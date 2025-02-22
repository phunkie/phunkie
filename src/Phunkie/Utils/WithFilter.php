<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

/**
 * Provides conditional filtering for safe operations.
 * 
 * WithFilter creates a filtered view of a Traversable collection that
 * ensures operations are only applied to elements meeting a condition.
 * This is particularly useful for preventing unsafe operations.
 *
 * Example:
 * ```php
 * // Safe division for positive numbers
 * $numbers = ImmList(2, -1, 4, 0, 3);
 * $result = $numbers->withFilter(fn($x) => $x > 0)  // Only positive numbers
 *                   ->map(fn($x) => 1/$x);          // Safe division
 * 
 * // Chaining multiple conditions
 * $strings->withFilter(fn($s) => strlen($s) > 0)    // Non-empty strings
 *         ->map(fn($s) => strtoupper($s));         // Safe uppercase
 * ```
 *
 * The filter is applied before any subsequent operations, ensuring
 * they only see elements that meet the condition.
 *
 * @see Traversable The source collection type
 */
class WithFilter
{
    /** @var Traversable The collection being filtered */
    private $filterable;

    /** @var callable The filter predicate */
    private $filter;

    /**
     * Creates a new filtered view.
     *
     * @param Traversable $filterable The collection to filter
     * @param callable $filter The predicate function
     */
    public function __construct(Traversable $filterable, callable $filter)
    {
        $this->filterable = $filterable;
        $this->filter = $filter;
    }

    /**
     * Maps a function over the filtered elements.
     * 
     * Applies the filter first, then maps only over elements
     * that passed the filter.
     *
     * @param callable $f The function to apply
     * @return Traversable The mapped filtered collection
     */
    public function map(callable $f)
    {
        return $this->filterable->filter($this->filter);
    }
}
