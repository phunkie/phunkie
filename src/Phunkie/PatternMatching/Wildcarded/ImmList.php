<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching\Wildcarded;

/**
 * Wildcard pattern matching for ImmList.
 * 
 * This class enables pattern matching against ImmList structures with
 * wildcarded head and/or tail components. It allows matching list
 * structures while ignoring specific values.
 *
 * Example:
 * ```php
 * // Match list with any head and specific tail
 * $match = new PMatch(ImmList(1, 2, 3));
 * if ($match(new ImmList(_, ImmList(2, 3)))) {
 *     // Matches because tail matches [2,3]
 * }
 * 
 * // Match list with specific head and any tail
 * if ($match(new ImmList(1, _))) {
 *     // Matches because head is 1
 * }
 * ```
 *
 * @see \Phunkie\Types\ImmList The ImmList type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class ImmList
{
    /** @var mixed The head pattern (can be wildcard _) */
    public $head;

    /** @var mixed The tail pattern (can be wildcard _) */
    public $tail;

    /**
     * Creates a new ImmList pattern with wildcardable components.
     *
     * @param mixed $head Head pattern or wildcard
     * @param mixed $tail Tail pattern or wildcard
     */
    public function __construct($head, $tail)
    {
        $this->head = $head;
        $this->tail = $tail;
    }
}
