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
 * Represents the left (error/failure) side of an Either.
 *
 * Left is used to represent a failure or error value in an Either type.
 * Operations like map and flatMap will skip Left values.
 *
 * Example:
 * ```php
 * $left = Left("error");
 * $left->get();          // "error"
 * $left->isLeft();       // true
 * $left->map(fn($x) => $x * 2);   // Left("error") - unchanged
 * ```
 *
 * @template L
 * @extends Either<L, mixed>
 */
final class Left extends Either
{
    /**
     * Prevents cloning of Left instances.
     */
    private function __clone()
    {
    }

    /**
     * Creates a new Left instance.
     *
     * @template T
     * @param T $value The error/failure value to wrap
     * @return Left<T>
     * @throws \TypeError if more than one argument is provided
     */
    public static function instance()
    {
        $numArgs = func_num_args();
        return match ($numArgs) {
            1 => new Left(func_get_arg(0)),
            default => throw new \TypeError(sprintf("Left must take exactly 1 argument, %d given", $numArgs))
        };
    }

    /**
     * Returns the default value, ignoring the Left's value.
     *
     * @template T
     * @param T $default The default value to return
     * @return T The default value
     */
    public function getOrElse($default)
    {
        return $default;
    }

    /**
     * Always returns false for Left.
     *
     * @return bool false
     */
    public function isRight(): bool
    {
        return false;
    }

    /**
     * Always returns true for Left.
     *
     * @return bool true
     */
    public function isLeft(): bool
    {
        return true;
    }
}
