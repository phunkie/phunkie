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

use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;

/**
 * Interface for traversable collections with functional operations.
 * 
 * Traversable extends both Functor and Monad to provide a rich set of
 * functional operations for collections. This enables:
 * - Mapping over elements (from Functor)
 * - Chaining operations (from Monad)
 * - Filtering elements
 * - Conditional filtering with WithFilter
 * - Side-effecting iteration
 *
 * Example:
 * ```php
 * $collection->map(fn($x) => $x * 2)        // Double all elements
 *           ->filter(fn($x) => $x > 10)     // Keep values > 10
 *           ->flatMap(fn($x) => Some($x));  // Chain with monadic op
 * 
 * // Conditional filtering
 * $collection->withFilter(fn($x) => $x > 0) // Only positive numbers
 *           ->map(fn($x) => 1/$x);         // Safe division
 * 
 * // Side effects
 * $collection->withEach(function($x) {
 *     echo $x; // Print each element
 * });
 * ```
 *
 * @see Functor For map operations
 * @see Monad For flatMap operations
 * @see WithFilter For conditional filtering
 */
interface Traversable extends Functor, Monad
{
    /**
     * Filters elements using a predicate.
     * 
     * @param callable $filter Function returning bool
     * @return Traversable New collection with filtered elements
     */
    public function filter(callable $filter): Traversable;

    /**
     * Creates a conditional filter for chaining operations.
     * 
     * @param callable $filter Function returning bool
     * @return WithFilter Filtered view of collection
     */
    public function withFilter(callable $filter): WithFilter;

    /**
     * Executes a function for each element.
     * Used for side effects like printing.
     * 
     * @param callable $block Function to execute on each element
     */
    public function withEach(callable $block);
}
