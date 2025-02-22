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
 * Represents an Option that contains no value.
 *
 * None is a singleton representing the absence of a value.
 *
 * Example:
 * ```php
 * $none = None();
 * $none->getOrElse(42); // 42
 * $none->isEmpty();     // true
 * ```
 *
 * @template A
 * @extends Option<A>
 */
final class None extends Option
{
    private static $instance;

    /**
     * Prevents cloning of the None singleton.
     */
    private function __clone()
    {
    }

    /**
     * Returns the None singleton instance.
     *
     * @return None The singleton instance
     */
    public static function instance()
    {
        return self::$instance == null ? self::$instance = new None() : self::$instance;
    }

    /**
     * Returns the default value since None contains no value.
     *
     * @template B
     * @param B $t The default value
     * @return B The default value
     */
    public function getOrElse($t)
    {
        return $t;
    }

    /**
     * Always returns false for None.
     *
     * @return bool false
     */
    public function isDefined(): bool
    {
        return false;
    }

    /**
     * Always returns true for None.
     *
     * @return bool true
     */
    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * Returns the type representation.
     *
     * @return string Always returns "None"
     */
    public function showType(): string
    {
        return "None";
    }
}
