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
 * Represents an Option that contains a value.
 *
 * Some is a container for a non-null value of type A.
 *
 * Example:
 * ```php
 * $some = Some(42);
 * $some->get();       // 42
 * $some->isDefined(); // true
 * ```
 *
 * @template A
 * @extends Option<A>
 */
final class Some extends Option
{
    /**
     * Prevents cloning of Some instances.
     */
    private function __clone()
    {
    }

    /**
     * Creates a new Some instance.
     *
     * @template B
     * @param B|null $value [optional] The value to wrap (defaults to Unit)
     * @return Some<B>
     * @throws \TypeError if more than one argument is provided
     */
    public static function instance() { $numArgs = func_num_args(); return match ($numArgs) {
        0 => new Some(Unit()),
        1 => new Some(func_get_arg(0)),
        default => throw new \TypeError(sprintf("Option must take exactly 1 argument, %d given", $numArgs)) };
    }

    /**
     * Returns the contained value, ignoring the default.
     *
     * @template B
     * @param B $t Ignored default value
     * @return A The contained value
     */
    public function getOrElse($t)
    {
        return $this->t;
    }

    /**
     * Returns the contained value.
     *
     * @return A The contained value
     */
    public function get()
    {
        return $this->t;
    }

    /**
     * Always returns true for Some.
     *
     * @return bool true
     */
    public function isDefined(): bool
    {
        return true;
    }

    /**
     * Always returns false for Some.
     *
     * @return bool false
     */
    public function isEmpty(): bool
    {
        return false;
    }
}
