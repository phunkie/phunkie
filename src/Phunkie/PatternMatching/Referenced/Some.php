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
 * Referenced pattern matching for Some values.
 * 
 * This class enables pattern matching with value extraction for Some types.
 * It captures the value inside a Some into a reference variable during matching.
 *
 * Example:
 * ```php
 * // Pattern matching with value extraction
 * $x = null;
 * $match = new PMatch(Some(42));
 * 
 * if ($match(new Some($x))) {
 *     echo $x; // prints "42"
 * }
 * 
 * // Nested pattern matching
 * $match = new PMatch(Some(Some(1)));
 * if ($match(new Some(new Some($x)))) {
 *     echo $x; // prints "1"
 * }
 * ```
 *
 * @see \Phunkie\Types\Some The Some type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class Some
{
    /** @var mixed Reference to store the extracted value */
    public $value;

    /**
     * Creates a new Some reference pattern.
     *
     * @param mixed &$value Reference that will receive the Some's value
     */
    public function __construct(&$value)
    {
        $this->value = &$value;
    }
}
