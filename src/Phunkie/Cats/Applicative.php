<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use Phunkie\Types\Kind;

/**
 * Represents functors that can lift pure values.
 * 
 * Applicative extends Apply with the ability to lift pure values into the functor
 * context. This is also known as 'return' in some languages.
 *
 * Laws:
 * 1. Identity: pure(id).apply(v) == v
 * 2. Homomorphism: pure(f).apply(pure(x)) == pure(f(x))
 * 3. Interchange: u.apply(pure(y)) == pure(f => f(y)).apply(u)
 *
 * Example:
 * ```php
 * Option::pure(42);        // Some(42)
 * ImmList::pure("hello");  // List("hello")
 * ```
 *
 * @template A
 * @extends Apply<A>
 */
interface Applicative extends Apply
{
    /**
     * Lifts a pure value into the applicative context.
     *
     * @template B
     * @param B $a The value to lift
     * @return Applicative<B> The value wrapped in the applicative
     */
    public function pure($a): Applicative;
}
