<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Unit;

use Phunkie\Algebra\Eq;

/**
 * Equality operations for Unit.
 *
 * Unit is a singleton type with only one value. Therefore, all Units
 * are equal to each other. This makes Unit's equality trivial but lawful.
 *
 * The equality satisfies:
 * - Reflexivity: x.eqv(x) == true
 * - Symmetry: x.eqv(y) == y.eqv(x)
 * - Transitivity: if x.eqv(y) and y.eqv(z) then x.eqv(z)
 *
 * @mixin \Phunkie\Types\Unit
 */
trait UnitEqOps
{
    use Eq;

    /**
     * Tests structural equality with another Unit.
     *
     * Since Unit is a singleton type with exactly one value,
     * all Unit values are equal to each other.
     *
     * Example:
     * ```php
     * Unit()->eqv(Unit());  // true
     *
     * $a = Unit();
     * $b = Unit();
     * $a->eqv($b);  // true
     * ```
     *
     * @param self $rhs The Unit to compare against
     * @return bool Always returns true for Unit values
     */
    public function eqv(self $rhs): bool
    {
        // Since Unit is a singleton type, all Units are equal
        return true;
    }
}
