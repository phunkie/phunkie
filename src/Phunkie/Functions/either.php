<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\Either;
    use Phunkie\Types\Left;
    use Phunkie\Types\Right;

    /**
     * Creates a Right value (success case).
     *
     * Right represents a successful computation or value.
     * Operations like map and flatMap will be applied.
     *
     * Example:
     * ```php
     * Right(42);              // Right(42)
     * Right("success");       // Right("success")
     * Right([1,2,3]);         // Right([1,2,3])
     * ```
     *
     * @template R
     * @param R $value Success value to wrap
     * @return Either<mixed,R> Right containing value
     */
    function Right(...$value): Either
    {
        return Right::instance(...$value);
    }

    /**
     * Creates a Left value (error/failure case).
     *
     * Left represents a failed computation or error.
     * Operations like map and flatMap will be skipped.
     *
     * Example:
     * ```php
     * Left("error");              // Left("error")
     * Left("Division by zero");   // Left("Division by zero")
     * Left(404);                  // Left(404)
     * ```
     *
     * @template L
     * @param L $value Error/failure value to wrap
     * @return Either<L,mixed> Left containing error value
     */
    function Left(...$value): Either
    {
        return Left::instance(...$value);
    }
}

namespace Phunkie\Functions\either {

    use Phunkie\Types\Either;
    use Phunkie\Types\Option;

    const isRight = "\\Phunkie\\Functions\\either\\isRight";
    /**
     * Checks if an Either is a Right.
     *
     * Returns true for Right, false for Left.
     *
     * Example:
     * ```php
     * isRight(Right(42));      // true
     * isRight(Left("error"));  // false
     * ```
     *
     * @template L
     * @template R
     * @param Either<L,R> $either Either to check
     * @return bool True if Right
     */
    function isRight(Either $either): bool
    {
        return $either->isRight();
    }

    const isLeft = "\\Phunkie\\Functions\\either\\isLeft";
    /**
     * Checks if an Either is a Left.
     *
     * Returns true for Left, false for Right.
     *
     * Example:
     * ```php
     * isLeft(Left("error"));   // true
     * isLeft(Right(42));       // false
     * ```
     *
     * @template L
     * @template R
     * @param Either<L,R> $either Either to check
     * @return bool True if Left
     */
    function isLeft(Either $either): bool
    {
        return $either->isLeft();
    }

    const fromRight = "\\Phunkie\\Functions\\either\\fromRight";
    /**
     * Gets the value from a Right.
     *
     * Returns the contained value if Right,
     * throws Error if Left.
     *
     * Example:
     * ```php
     * fromRight(Right(42));         // 42
     * fromRight(Left("error"));     // throws Error
     * ```
     *
     * @template L
     * @template R
     * @param Either<L,R> $either Either to get value from
     * @return R The Right value
     * @throws \Error if Left
     */
    function fromRight(Either $either)
    {
        if (isLeft($either)) {
            throw new \Error("Cannot get a value from Left.");
        }
        return $either->get();
    }

    const fromLeft = "\\Phunkie\\Functions\\either\\fromLeft";
    /**
     * Gets the value from a Left.
     *
     * Returns the contained value if Left,
     * throws Error if Right.
     *
     * Example:
     * ```php
     * fromLeft(Left("error"));      // "error"
     * fromLeft(Right(42));          // throws Error
     * ```
     *
     * @template L
     * @template R
     * @param Either<L,R> $either Either to get value from
     * @return L The Left value
     * @throws \Error if Right
     */
    function fromLeft(Either $either)
    {
        if (isRight($either)) {
            throw new \Error("Cannot get a value from Right.");
        }
        return $either->get();
    }

    const eitherToOption = "\\Phunkie\\Functions\\either\\eitherToOption";
    /**
     * Converts an Either to an Option.
     *
     * Returns Some containing the value if Right,
     * None if Left.
     *
     * Example:
     * ```php
     * eitherToOption(Right(42));        // Some(42)
     * eitherToOption(Left("error"));    // None
     * ```
     *
     * @template L
     * @template R
     * @param Either<L,R> $either Either to convert
     * @return Option<R> Resulting Option
     */
    function eitherToOption(Either $either): Option
    {
        return $either->toOption();
    }
}
