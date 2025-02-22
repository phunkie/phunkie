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
 * Represents functors that can be flattened and chained.
 * 
 * FlatMap extends Functor with the ability to sequence operations that return
 * values in the functor context. This is particularly useful for handling
 * nested structures and composing computations.
 *
 * Laws:
 * 1. Associativity: fa.flatMap(f).flatMap(g) == fa.flatMap(x => f(x).flatMap(g))
 *
 * Example:
 * ```php
 * $opt = Some(42);
 * $opt->flatMap(fn($x) => Some($x * 2));  // Some(84)
 * $opt->flatMap(fn($x) => None());        // None
 * 
 * // Handling nested structures
 * $nested = Some(Some(42));
 * $nested->flatten();  // Some(42)
 * ```
 *
 * @template A
 * @extends Functor<A>
 */
interface FlatMap extends Functor
{
    /**
     * Chains operations that return values in the functor context.
     *
     * @template B
     * @param callable(A):static<B> $f The function returning a new functor
     * @return static<B> The result of applying and flattening
     */
    public function flatMap(callable $f): Kind;

    /**
     * Flattens a nested functor structure.
     *
     * @return static<T> where T is the type parameter of the inner functor
     */
    public function flatten(): Kind;
}
