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
 * Pattern matching for non empty lists with head and tail.
 *
 * This class enables pattern matching and value extraction for non empty lists
 * by capturing both the head element and the remaining tail into reference
 * variables. It is the counterpart of ListWithTail for a NonEmptyList, and
 * matches nothing else: a list that merely happens to hold something is not a
 * NonEmptyList, and is not matched by this pattern.
 *
 * Example:
 * ```php
 * // Pattern matching with head and tail extraction
 * $head = $tail = null;
 * $match = new PMatch(Nel(1, 2, 3));
 *
 * if ($match(new NonEmptyList($head, $tail))) {
 *     echo "$head and "; // prints "1 and "
 *     echo $tail->mkString(); // prints "2,3"
 * }
 * ```
 *
 * @see \Phunkie\Types\NonEmptyList The list type being matched
 * @see \Phunkie\PatternMatching\Referenced\ListWithTail The same, for any list
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class NonEmptyList
{
    /** @var mixed Reference to store the head value */
    public $head;

    /** @var mixed Reference to store the tail list */
    public $tail;

    /**
     * Creates a new non empty list pattern with head and tail.
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
