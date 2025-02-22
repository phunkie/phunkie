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

use Phunkie\Types\ImmMap;

/**
 * Property access for pattern matching.
 * 
 * Wildcard provides a way to access properties of objects during pattern matching.
 * It can extract values from:
 * - Public properties
 * - Getter methods
 * - ImmMap entries
 *
 * Example:
 * ```php
 * // Access via public property
 * class Person { public $name = "Alice"; }
 * $person = new Person();
 * $w = new Wildcard("name");
 * $w($person); // returns "Alice"
 * 
 * // Access via getter method
 * class User { 
 *     public function getName() { return "Bob"; }
 * }
 * $user = new User();
 * $w = new Wildcard("name");
 * $w($user); // returns "Bob"
 * 
 * // Access via ImmMap
 * $map = ImmMap("name" => "Charlie");
 * $w = new Wildcard("name");
 * $w($map); // returns "Charlie"
 * ```
 */
class Wildcard
{
    private $member;

    /**
     * Creates a new property accessor.
     *
     * @param string $member The property/method name to access
     */
    public function __construct(string $member)
    {
        $this->member = $member;
    }

    /**
     * Attempts to extract a value from the given data.
     * 
     * Tries multiple access methods in this order:
     * 1. Getter method (get{Property})
     * 2. Public property access
     * 3. ImmMap key lookup
     * 
     * Returns None if no value could be accessed.
     *
     * @param mixed $data The object/map to extract from
     * @return mixed|None The extracted value or None
     */
    public function __invoke($data)
    {
        if (is_object($data) && method_exists($data, "get" . $this->member)) {
            return $data->{"get$this->member"}();
        } elseif (is_object($data) && (new \ReflectionProperty($data, $this->member))->isPublic()) {
            return $data->{$this->member};
        } elseif ($data instanceof ImmMap && $data->offsetExists($this->member)) {
            return $data->get($this->member);
        }
        return None();
    }
}
