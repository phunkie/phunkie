<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Algebra;

/**
 * Represents types that can be compared for equality.
 * 
 * Eq defines an equivalence relation between values. Types implementing Eq
 * must ensure their implementation is:
 * - Reflexive: a.eqv(a) == true
 * - Symmetric: a.eqv(b) == b.eqv(a)
 * - Transitive: if a.eqv(b) and b.eqv(c) then a.eqv(c)
 *
 * Example:
 * ```php
 * class Point implements Eq {
 *     private $x, $y;
 *     public function eqv(self $other): bool {
 *         return $this->x === $other->x && $this->y === $other->y;
 *     }
 * }
 * ```
 *
 * @template A
 */
trait Eq
{
    /**
     * Tests if two values are equivalent.
     *
     * @param self $rhs The right-hand side value to compare with
     * @return bool True if the values are equivalent
     */
    abstract public function eqv(self $rhs): bool;
}
