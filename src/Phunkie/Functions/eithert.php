<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phunkie\Cats\EitherT;

/**
 * Functions for working with EitherT monad transformer.
 *
 * EitherT combines Either with another monad (like IO or List),
 * allowing you to work with computations that might fail in the context of that monad.
 */

/**
 * Creates an EitherT monad transformer.
 *
 * Wraps a monad containing an Either into EitherT.
 * Useful for composing Either with other monads.
 *
 * Example:
 * ```php
 * // Either inside IO
 * $safeDivide = fn($a, $b) => IO(fn() => $b === 0 ? Left("div by zero") : Right($a / $b));
 * $computation = EitherT($safeDivide(10, 2))->map(fn($x) => $x * 2);
 *
 * // Either inside List
 * $results = ImmList(Right(1), Left("error"), Right(3));
 * $doubled = EitherT($results)->map(fn($x) => $x * 2)->getValue();
 * // ImmList(Right(2), Left("error"), Right(6))
 *
 * // Chaining computations
 * $result = EitherT(IO(fn() => Right(5)))
 *     ->flatMap(fn($x) => EitherT(IO(fn() => Right($x * 2))))
 *     ->flatMap(fn($x) => EitherT(IO(fn() => Right($x + 1))))
 *     ->getOrElse(0);
 * // Runs the computation: (5 * 2) + 1 = 11
 *
 * // Error short-circuits
 * $result = EitherT(IO(fn() => Right(5)))
 *     ->flatMap(fn($x) => EitherT(IO(fn() => Left("error"))))
 *     ->flatMap(fn($x) => EitherT(IO(fn() => Right($x + 1))))
 *     ->getOrElse(0);
 * // Returns 0 because of the Left("error")
 * ```
 *
 * @template M,L,R
 * @param M<Either<L,R>> $monad Monad containing Either
 * @return EitherT<M,L,R> Either transformer
 */
function EitherT($value)
{
    return new EitherT($value);
}
