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

use Phunkie\Types\Either;
use RuntimeException;
use function Phunkie\Functions\semigroup\combine;

/**
 * Monoid operations for Either.
 *
 * Provides zero element and combination for Either values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherMonoidOps
{
    /**
     * Returns the zero element for Either.
     *
     * The identity element is Left("").
     * This satisfies: combine(zero(), x) == x
     *
     * Example:
     * ```php
     * Right(42)->zero();  // Left("")
     * Left("error")->zero(); // Left("")
     * ```
     *
     * @return Either<L,R> Left with empty string
     */
    public function zero()
    {
        return Left("");
    }

    /**
     * Combines two Either values using their Monoid instances.
     *
     * Combination rules:
     * - Left + Either => Either (Left is absorbed)
     * - Right + Left => Right (Left is absorbed)
     * - Right + Right => Right(combine(a, b)) (values combined)
     *
     * Example:
     * ```php
     * Right([1, 2])->combine(Right([3, 4]));  // Right([1, 2, 3, 4])
     * Right([1])->combine(Left("error"));     // Right([1])
     * Left("error")->combine(Right([1]));     // Right([1])
     * Left("a")->combine(Left("b"));          // Left("b")
     * Right("hello")->combine(Right(" world")); // Right("hello world")
     * ```
     *
     * @param Either<L,R> $b The Either to combine with
     * @return Either<L,R> Combined Either
     * @throws RuntimeException If used on non-Either type
     */
    public function combine(Either $b): Either
    {
        return match (true) {
            $this->notAnEither() => throw new RuntimeException("Either ops imported to non-Either"),
            $this->isLeft() => $b,
            $b->isLeft() => $this,
            default => Right(combine($this->get(), $b->get()))
        };
    }

    private function notAnEither(): bool
    {
        return !$this instanceof Either;
    }
}
