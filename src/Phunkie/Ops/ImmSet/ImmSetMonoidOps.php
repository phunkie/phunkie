<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmSet;

use Phunkie\Types\ImmSet;

/**
 * Monoid operations for ImmSet.
 *
 * Provides monoid structure where:
 * - zero (identity element) is the empty set
 * - combine (binary operation) is set union
 *
 * Monoid laws:
 * - Left identity: zero()->combine(s) == s
 * - Right identity: s->combine(zero()) == s
 * - Associativity: a->combine(b)->combine(c) == a->combine(b->combine(c))
 *
 * @template A The element type
 * @mixin \Phunkie\Types\ImmSet
 */
trait ImmSetMonoidOps
{
    abstract public function union(ImmSet $set): ImmSet;

    /**
     * Returns the identity element for the Monoid (empty set).
     *
     * The zero element satisfies:
     * - zero()->combine(s) == s (left identity)
     * - s->combine(zero()) == s (right identity)
     *
     * Example:
     * ```php
     * $empty = ImmSet(1, 2, 3)->zero();  // ImmSet()
     * $s = ImmSet(1, 2);
     * $s->combine($empty);  // ImmSet(1, 2)
     * $empty->combine($s);  // ImmSet(1, 2)
     * ```
     *
     * @return ImmSet<A> An empty set
     */
    public function zero(): ImmSet
    {
        return ImmSet();
    }

    /**
     * Combines two sets using union operation.
     *
     * Returns a new set containing all unique elements from both sets.
     * This is the monoid binary operation for sets.
     *
     * Satisfies associativity:
     * a->combine(b)->combine(c) == a->combine(b->combine(c))
     *
     * Example:
     * ```php
     * $s1 = ImmSet(1, 2);
     * $s2 = ImmSet(2, 3);
     * $s3 = ImmSet(3, 4);
     *
     * $s1->combine($s2);  // ImmSet(1, 2, 3)
     *
     * // Associativity
     * $s1->combine($s2)->combine($s3);  // ImmSet(1, 2, 3, 4)
     * $s1->combine($s2->combine($s3));  // ImmSet(1, 2, 3, 4)
     * ```
     *
     * @param ImmSet<A> $b The set to combine with
     * @return ImmSet<A> The union of both sets
     */
    public function combine(ImmSet $b): ImmSet
    {
        return $this->union($b);
    }
}
