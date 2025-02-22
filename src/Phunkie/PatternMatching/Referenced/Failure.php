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
 * Referenced pattern matching for Failure values.
 * 
 * This class enables pattern matching with value extraction for Failure types.
 * It captures the value inside a Failure into a reference variable during matching.
 *
 * Example:
 * ```php
 * // Pattern matching with value extraction
 * $x = null;
 * $match = new PMatch(Failure("error"));
 * 
 * if ($match(new Failure($x))) {
 *     echo $x; // prints "error"
 * }
 * 
 * // Multiple matches
 * $match = new PMatch(Failure(42));
 * 
 * match ($match) {
 *     case new Failure($n) => "Failed with $n",
 *     default => "Not a failure"
 * };
 * ```
 *
 * @see \Phunkie\Validation\Failure The Failure type being matched
 * @see \Phunkie\PatternMatching\PMatch The pattern matcher
 */
class Failure
{
    /** @var mixed Reference to store the extracted value */
    public $value;

    /**
     * Creates a new Failure reference pattern.
     *
     * @param mixed &$value Reference that will receive the Failure's value
     */
    public function __construct(&$value)
    {
        $this->value = &$value;
    }
}
