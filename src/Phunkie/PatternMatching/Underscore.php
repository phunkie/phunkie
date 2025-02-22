<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching;

/**
 * Underscore wildcard pattern matching.
 * 
 * This class provides wildcard pattern matching functionality through
 * the special underscore (_) symbol. It allows matching any value
 * and accessing object members through magic property access.
 *
 * Example:
 * ```php
 * // Match any value
 * $match = new PMatch($value);
 * if ($match(_)) { ... }
 * 
 * // Match any value in a Some
 * if ($match(Some(_))) { ... }
 * 
 * // Match and access object members
 * class Person { public $name; }
 * $person = new Person();
 * $person->name = "Alice";
 * 
 * if (_->name($person) == "Alice") { ... }
 * ```
 *
 * The underscore acts as a placeholder that matches any value.
 * When accessing properties through _->property, it creates a Wildcard
 * that can extract that property from matched objects.
 */
class Underscore
{
    /**
     * Creates a Wildcard for accessing object members.
     * 
     * When accessing a non-existent property of Underscore,
     * this magic method creates a Wildcard that can extract
     * that property from matched objects.
     * @see Wildcard
     *
     * @param string $member The property name to access
     * @return Wildcard A wildcard matcher for the property
     */
    public function __get($member): Wildcard
    {
        return new Wildcard($member);
    }
}
