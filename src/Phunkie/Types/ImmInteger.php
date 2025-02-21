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
 * An immutable integer wrapper.
 * 
 * ImmInteger provides a type-safe wrapper around PHP integers, ensuring
 * immutability and proper type information in generic contexts.
 *
 * Example:
 * ```php
 * $num = new ImmInteger(42);
 * echo $num->get();  // 42
 * ```
 */
final class ImmInteger
{
    private $value;

    /**
     * Creates a new immutable integer.
     *
     * @param int $value The integer value to wrap
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the wrapped integer value.
     *
     * @return int The contained integer
     */
    public function get()
    {
        return $this->value;
    }
}
