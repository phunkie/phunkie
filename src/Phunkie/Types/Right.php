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
 * Represents the right (success) side of an Either.
 *
 * Right is used to represent a successful value in an Either type.
 * Operations like map and flatMap will be applied to Right values.
 *
 * Example:
 * ```php
 * $right = Right(42);
 * $right->get();         // 42
 * $right->isRight();     // true
 * $right->map(fn($x) => $x * 2);  // Right(84)
 * ```
 *
 * @template R
 * @extends Either<mixed, R>
 */
final class Right extends Either
{
    /**
     * Prevents cloning of Right instances.
     */
    private function __clone()
    {
    }

    /**
     * Creates a new Right instance.
     *
     * @template T
     * @param T $value The success value to wrap
     * @return Right<T>
     * @throws \TypeError if more than one argument is provided
     */
    public static function instance()
    {
        $numArgs = func_num_args();
        return match ($numArgs) {
            1 => new Right(func_get_arg(0)),
            default => throw new \TypeError(sprintf("Right must take exactly 1 argument, %d given", $numArgs))
        };
    }

    /**
     * Returns the contained value, ignoring the default.
     *
     * @template T
     * @param T $default Ignored default value
     * @return R The contained value
     */
    public function getOrElse($default)
    {
        return $this->value;
    }

    /**
     * Always returns true for Right.
     *
     * @return bool true
     */
    public function isRight(): bool
    {
        return true;
    }

    /**
     * Always returns false for Right.
     *
     * @return bool false
     */
    public function isLeft(): bool
    {
        return false;
    }
}
