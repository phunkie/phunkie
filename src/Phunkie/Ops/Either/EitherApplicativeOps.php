<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Either;

use BadMethodCallException;
use Phunkie\Cats\Applicative;
use Phunkie\Types\Kind;
use Phunkie\Types\Left;
use Phunkie\Types\Right;

/**
 * Applicative operations for Either.
 *
 * Provides applicative functor operations for combining Either values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherApplicativeOps
{
    use EitherMonadOps;

    /**
     * Lifts a value into a successful Either context.
     *
     * Creates a Right containing the given value.
     * This is the Applicative pure operation.
     *
     * Example:
     * ```php
     * Right(42)->pure(100);  // Right(100)
     * Left("error")->pure(100); // Right(100)
     * ```
     *
     * @template A
     * @param A $a Value to lift
     * @return Applicative|Either<L,A>
     */
    public function pure($a): Applicative
    {
        return Right($a);
    }

    /**
     * Applies a function wrapped in an Either to this Either's value.
     *
     * Implements the Applicative apply operation.
     * Short-circuits on the first Left encountered.
     *
     * Example:
     * ```php
     * $add = fn($a) => fn($b) => $a + $b;
     * Right(5)->apply(Right($add));  // Right(fn(5))
     * Right(5)->apply(Left("error")); // Left("error")
     * Left("error")->apply(Right($add)); // Left("error")
     * ```
     *
     * @template B
     * @param Kind|Either<L,callable(R):B> $ff Either containing a function
     * @return Kind|Either<L,B>
     * @throws BadMethodCallException If $ff is not a callable Right or Left
     */
    public function apply(Kind $ff): Kind
    {
        return match (true) {
            $this->isLeft() => $this,
            $ff instanceof Left => $ff,
            $ff instanceof Right && is_callable($ff->get()) => $this->map($ff->get()),
            default => throw new BadMethodCallException()
        };
    }

    /**
     * Combines two Either values with a binary function.
     *
     * Applies the function to both Right values.
     * Short-circuits on the first Left encountered.
     *
     * Example:
     * ```php
     * Right(5)->map2(Right(3), fn($a, $b) => $a + $b);  // Right(8)
     * Right(5)->map2(Left("error"), fn($a, $b) => $a + $b); // Left("error")
     * Left("error")->map2(Right(3), fn($a, $b) => $a + $b); // Left("error")
     * ```
     *
     * @template B The type of the second Either's value
     * @template C The result type
     * @param Kind|Either<L,B> $fb The second Either
     * @param callable(R,B):C $f Binary function to combine values
     * @return Kind|Either<L,C>
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn($b) => fn($a) => $f($a, $b)));
    }
}
