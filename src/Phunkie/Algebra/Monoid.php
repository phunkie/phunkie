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
 * Represents a Semigroup with an identity element.
 * 
 * A Monoid is a Semigroup with a special value 'zero' (identity) such that:
 * combine(zero, a) == a == combine(a, zero)
 *
 * Example:
 * ```php
 * // String concatenation with empty string as identity
 * $m->zero();           // ""
 * $m->combine("a", ""); // "a"
 * 
 * // Integer addition with 0 as identity
 * $m->zero();        // 0
 * $m->combine(5, 0); // 5
 * ```
 *
 * @template T
 * @extends Semigroup<T>
 */
interface Monoid extends Semigroup
{
    /**
     * Returns the identity element for this monoid.
     *
     * @return T The identity element
     */
    public function zero();
}
