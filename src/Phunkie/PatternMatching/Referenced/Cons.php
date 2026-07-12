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
 * Pattern matching for cons cells, binding head and tail.
 *
 * A Cons is a list built from an element and the list after it, and this
 * pattern takes it back apart into the two. It matches a Cons and nothing else:
 * an ordinary list is matched with ListWithTail, even when it holds something.
 *
 * Example:
 * ```php
 * // Pattern matching with head and tail extraction
 * $head = $tail = null;
 * $match = new PMatch(Cons(1, ImmList(2, 3)));
 *
 * if ($match(new Cons($head, $tail))) {
 *     echo "$head and "; // prints "1 and "
 *     echo $tail->mkString(); // prints "2,3"
 * }
 * ```
 *
 * @see \Phunkie\Types\Cons The list type being matched
 * @see \Phunkie\PatternMatching\Referenced\ListWithTail The same, for any list
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class Cons
{
    /** @var mixed Reference to store the head value */
    public $head;

    /** @var mixed Reference to store the tail list */
    public $tail;

    /**
     * Creates a new cons cell pattern with head and tail.
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
