<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching\Referenced;

/**
 * Pattern matching for single-element lists.
 * 
 * This class enables pattern matching and value extraction for lists with
 * exactly one element (no tail). It captures the head element into a reference
 * variable and ensures there is no tail.
 *
 * Example:
 * ```php
 * // Pattern matching with single element
 * $x = null;
 * $match = new PMatch(ImmList(42));
 * 
 * if ($match(new ListNoTail($x, Nil()))) {
 *     echo $x; // prints "42"
 * }
 * 
 * // Won't match lists with more elements
 * $match = new PMatch(ImmList(1, 2, 3));
 * if ($match(new ListNoTail($x, Nil()))) {
 *     // This won't execute
 * }
 * ```
 *
 * @see \Phunkie\Types\ImmList The list type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class ListNoTail
{
    /** @var mixed Reference to store the head value */
    public $head;

    /** @var mixed The tail (must be Nil) */
    public $tail;

    /**
     * Creates a new single-element list pattern.
     *
     * @param mixed &$x Reference that will receive the head value
     * @param mixed $xs The tail (must be Nil)
     */
    public function __construct(&$x, $xs)
    {
        $this->head = &$x;
        $this->tail = $xs;
    }
}
