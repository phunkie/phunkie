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
 * Represents computations that can be chained together.
 * 
 * Monad combines the capabilities of Applicative and FlatMap to provide
 * a powerful abstraction for sequencing computations. Monads must satisfy
 * additional laws beyond those of Applicative and FlatMap.
 *
 * Laws:
 * 1. Left identity: pure(a).flatMap(f) == f(a)
 * 2. Right identity: m.flatMap(pure) == m
 * 3. Associativity: (from FlatMap)
 *
 * Example:
 * ```php
 * // Option monad
 * $opt = Some(21);
 * $opt->flatMap(fn($x) => Some($x * 2))  // Some(42)
 *    ->flatMap(fn($x) => Some($x + 1));  // Some(43)
 * 
 * // List monad
 * $list = ImmList(1, 2);
 * $list->flatMap(fn($x) => ImmList($x, $x * 2));  // List(1, 2, 2, 4)
 * ```
 *
 * @template A
 * @extends Applicative<A>
 * @extends FlatMap<A>
 */
interface Monad extends Applicative, FlatMap
{
    /**
     * Flattens a nested monadic structure.
     * 
     * For monads, this operation must be consistent with flatMap:
     * m.flatten() == m.flatMap(identity)
     *
     * Example:
     * ```php
     * $nested = Some(Some(42));
     * $nested->flatten();  // Some(42)
     * 
     * $lists = ImmList(ImmList(1, 2), ImmList(3, 4));
     * $lists->flatten();   // List(1, 2, 3, 4)
     * ```
     *
     * @return static<T> where T is the type parameter of the inner monad
     */
    public function flatten(): Kind;
}
