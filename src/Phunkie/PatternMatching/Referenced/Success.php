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
 * Referenced pattern matching for Success values.
 * 
 * This class enables pattern matching with value extraction for Success types.
 * It captures the value inside a Success into a reference variable during matching.
 *
 * Example:
 * ```php
 * // Pattern matching with value extraction
 * $x = null;
 * $match = new PMatch(Success(42));
 * 
 * if ($match(new Success($x))) {
 *     echo $x; // prints "42"
 * }
 * 
 * // Using in validation context
 * $result = Success("valid");
 * if ($match(new Success($value))) {
 *     // Handle successful validation
 *     echo $value; // prints "valid"
 * }
 * ```
 *
 * @see \Phunkie\Validation\Success The Success type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class Success
{
    /** @var mixed Reference to store the extracted value */
    public $value;

    /**
     * Creates a new Success reference pattern.
     *
     * @param mixed &$value Reference that will receive the Success's value
     */
    public function __construct(&$value)
    {
        $this->value = &$value;
    }
}
