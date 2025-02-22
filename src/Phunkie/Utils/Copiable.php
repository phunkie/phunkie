<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

/**
 * Interface for objects that support field-specific copying.
 * 
 * Copiable provides a mechanism to create copies of objects with
 * specific fields modified. This is useful for implementing
 * immutable objects that need controlled mutation.
 *
 * Example:
 * ```php
 * class Person implements Copiable {
 *     private $name;
 *     private $age;
 * 
 *     public function copy(array $fields) {
 *         $copy = clone $this;
 *         foreach ($fields as $field => $value) {
 *             $copy->$field = $value;
 *         }
 *         return $copy;
 *     }
 * }
 * 
 * $person = new Person("Alice", 30);
 * $older = $person->copy(['age' => 31]); // Only age is changed
 * ```
 */
interface Copiable
{
    /**
     * Creates a copy with specified fields modified.
     * 
     * @param array $fields Associative array of field names and new values
     * @return static A new instance with modified fields
     */
    public function copy(array $fields);
}
