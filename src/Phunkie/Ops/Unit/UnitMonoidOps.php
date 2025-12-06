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

use Phunkie\Types\Unit;
use RuntimeException;

/**
 * Monoid operations for Unit.
 *
 * Unit forms a trivial monoid where:
 * - The identity element is Unit itself
 * - Combining any two Units always produces Unit
 *
 * This satisfies the monoid laws:
 * - Identity: combine(zero(), x) == x and combine(x, zero()) == x
 * - Associativity: combine(combine(a, b), c) == combine(a, combine(b, c))
 *
 * @mixin \Phunkie\Types\Unit
 */
trait UnitMonoidOps
{
    /**
     * Returns the zero element for Unit.
     *
     * The identity element for Unit is Unit itself.
     * This satisfies: combine(zero(), Unit()) == Unit()
     *
     * Example:
     * ```php
     * Unit()->zero();  // Unit()
     * ```
     *
     * @return Unit The zero element (Unit itself)
     */
    public function zero(): Unit
    {
        return Unit();
    }

    /**
     * Combines two Unit values.
     *
     * Since Unit has only one value, combining any two Units
     * always results in Unit. This makes Unit the trivial monoid.
     *
     * Example:
     * ```php
     * Unit()->combine(Unit());  // Unit()
     * ```
     *
     * @param Unit $b The Unit to combine with
     * @return Unit Always returns Unit
     * @throws RuntimeException If used on non-Unit type
     */
    public function combine(Unit $b): Unit
    {
        if (!$this instanceof Unit) {
            throw new RuntimeException("Unit ops imported to non-Unit");
        }

        return Unit();
    }
}
