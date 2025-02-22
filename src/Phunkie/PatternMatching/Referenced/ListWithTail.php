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
 * Pattern matching for lists with head and tail.
 * 
 * This class enables pattern matching and value extraction for lists by
 * capturing both the head element and the remaining tail into reference
 * variables. This allows destructuring lists into their components.
 *
 * Example:
 * ```php
 * // Pattern matching with head and tail extraction
 * $head = $tail = null;
 * $match = new PMatch(ImmList(1, 2, 3));
 * 
 * if ($match(new ListWithTail($head, $tail))) {
 *     echo "$head and "; // prints "1 and "
 *     echo $tail->mkString(); // prints "2,3"
 * }
 * 
 * // Nested pattern matching
 * $x = $y = $rest = null;
 * if ($match(new ListWithTail($x, new ListWithTail($y, $rest)))) {
 *     // $x = 1, $y = 2, $rest = ImmList(3)
 * }
 * ```
 *
 * @see \Phunkie\Types\ImmList The list type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class ListWithTail
{
    /** @var mixed Reference to store the head value */
    public $head;

    /** @var mixed Reference to store the tail list */
    public $tail;

    /**
     * Creates a new list pattern with head and tail.
     *
     * @param mixed &$x Reference that will receive the head value
     * @param mixed &$xs Reference that will receive the tail list
     */
    public function __construct(&$x, &$xs)
    {
        $this->head = &$x;
        $this->tail = &$xs;
    }
}
