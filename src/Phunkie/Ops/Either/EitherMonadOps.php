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

use Phunkie\Types\Kind;

/**
 * Monad operations for Either.
 *
 * Provides flatMap and flatten operations for chaining Either computations.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherMonadOps
{
    use EitherFunctorOps;

    /**
     * Chains Either computations.
     *
     * Applies a function that returns an Either to the Right value.
     * Short-circuits on Left, passing the error through unchanged.
     *
     * Example:
     * ```php
     * $safeDivide = fn($a, $b) => $b === 0 ? Left("div by zero") : Right($a / $b);
     *
     * Right(10)->flatMap(fn($x) => $safeDivide($x, 2));  // Right(5)
     * Right(10)->flatMap(fn($x) => $safeDivide($x, 0));  // Left("div by zero")
     * Left("error")->flatMap(fn($x) => $safeDivide($x, 2)); // Left("error")
     * ```
     *
     * @template B The result right type
     * @param callable(R):Either<L,B> $f Function returning Either
     * @return Kind|Either<L,B>
     */
    public function flatMap(callable $f): Kind
    {
        return $this->isLeft() ? $this : $f($this->get());
    }

    /**
     * Flattens nested Either values.
     *
     * Removes one level of nesting from Either<L,Either<L,R>>.
     * Recursively flattens nested Left values.
     *
     * Example:
     * ```php
     * flatten(Right(Right(42)));         // Right(42)
     * flatten(Left(Left("error")));      // Left("error")
     * flatten(Right(Left("inner error"))); // Left("inner error")
     * ```
     *
     * @return Kind|Either<L,R>
     */
    public function flatten(): Kind
    {
        if ($this->isLeft()) {
            // If Left contains another Left, recursively flatten
            if ($this->get() instanceof \Phunkie\Types\Left) {
                return $this->get()->flatten();
            }
            return $this;
        }
        // For Right, use flatMap with identity
        return $this->flatMap(fn($x) => $x);
    }
}
