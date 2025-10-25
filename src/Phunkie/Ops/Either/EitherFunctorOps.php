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

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;
use Phunkie\Types\Left;

/**
 * Functor operations for Either.
 *
 * Provides map operations that transform Right values while preserving Left values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherFunctorOps
{
    use FunctorOps;

    /**
     * Maps a function over the Right value.
     *
     * Transforms Right values while passing Left values through unchanged.
     * This is the core Functor operation for Either.
     *
     * Example:
     * ```php
     * Right(5)->map(fn($x) => $x * 2);  // Right(10)
     * Left("error")->map(fn($x) => $x * 2); // Left("error")
     * ```
     *
     * @template B The result type
     * @param callable(R):B $f Function to apply to Right value
     * @return Kind|Either<L,B>
     */
    public function map(callable $f): Kind
    {
        if ($this->isLeft()) {
            return $this;
        }
        return Right($f($this->get()));
    }

    /**
     * Invariant functor map.
     *
     * For Either, this is equivalent to map since Either is covariant.
     * The reverse function $g is unused.
     *
     * @template B The result type
     * @param callable(R):B $f The forward mapping function
     * @param callable(B):R $g The reverse mapping function (unused)
     * @return Kind|Either<L,B>
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }
}
