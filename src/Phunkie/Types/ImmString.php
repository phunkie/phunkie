<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

/**
 * An immutable string wrapper.
 * 
 * ImmString provides a type-safe wrapper around PHP strings, ensuring
 * immutability and proper type information in generic contexts.
 *
 * Example:
 * ```php
 * $str = new ImmString("hello");
 * echo $str->get();      // "hello"
 * echo (string)$str;     // "hello"
 * ```
 */
final class ImmString
{
    private $value;

    /**
     * Creates a new immutable string.
     *
     * @param string $value The string value to wrap
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the wrapped string value.
     *
     * @return string The contained string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Converts to string when used in string context.
     *
     * @return string The contained string
     */
    public function __toString()
    {
        return $this->value;
    }
}
