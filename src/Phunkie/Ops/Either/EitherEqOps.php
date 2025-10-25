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

use Phunkie\Algebra\Eq;

/**
 * Equality operations for Either.
 *
 * Provides structural equality comparison for Either values.
 *
 * @template L The left (error) type
 * @template R The right (success) type
 * @mixin \Phunkie\Types\Either
 */
trait EitherEqOps
{
    use Eq;

    /**
     * Tests structural equality with another Either.
     *
     * Two Either values are equal if:
     * - Both are Left with equal values, or
     * - Both are Right with equal values
     *
     * Uses PHP's == operator for value comparison.
     *
     * Example:
     * ```php
     * Right(42)->eqv(Right(42));        // true
     * Right(42)->eqv(Right(43));        // false
     * Right(42)->eqv(Left(42));         // false
     * Left("error")->eqv(Left("error")); // true
     * Left("a")->eqv(Left("b"));        // false
     * ```
     *
     * @param self $rhs The Either to compare against
     * @return bool True if structurally equal
     */
    public function eqv(self $rhs): bool
    {
        return $this == $rhs;
    }
}
