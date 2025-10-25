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
 * Represents types that support context-dependent computation.
 *
 * A Comonad is the dual of a Monad. Where Monad lets you put values into
 * a context, Comonad lets you extract values from a context and work with
 * neighborhoods of values.
 *
 * Comonads must satisfy three laws:
 * 1. Left identity: extract(duplicate(wa)) == wa
 * 2. Right identity: map(extract)(duplicate(wa)) == wa
 * 3. Associativity: duplicate(duplicate(wa)) == map(duplicate)(duplicate(wa))
 *
 * Common examples include:
 * - Pair (extract returns second element, first provides context)
 * - NonEmptyList (extract returns current focus)
 * - Store (extract evaluates at current position)
 *
 * Example:
 * ```php
 * $pair = Pair("context", 42);
 * $pair->extract();  // 42
 * $pair->duplicate(); // Pair("context", Pair("context", 42))
 * $pair->extend(fn($p) => $p->extract() * 2); // Pair("context", 84)
 * ```
 *
 * @template A
 */
interface Comonad extends Functor
{
    /**
     * Extracts the value from the comonadic context.
     *
     * This is the dual of Monad's pure/return operation.
     * For Pair, extracts the second element.
     *
     * Example:
     * ```php
     * Pair("label", 42)->extract(); // 42
     * ```
     *
     * @return A The extracted value
     */
    public function extract();

    /**
     * Wraps the comonad in another layer of itself.
     *
     * Creates a nested structure where the inner comonad is the same
     * as the outer one. This is the dual of Monad's flatten/join.
     *
     * Example:
     * ```php
     * Pair("x", 42)->duplicate(); // Pair("x", Pair("x", 42))
     * ```
     *
     * @return static<static<A>> A nested comonad
     */
    public function duplicate(): Kind;

    /**
     * Extends a comonadic computation.
     *
     * Applies a function that operates on the whole comonad
     * to produce a new value. This is the dual of Monad's flatMap/bind.
     *
     * Example:
     * ```php
     * $pair = Pair("prefix: ", 42);
     * $pair->extend(fn($p) => $p->_1 . $p->extract());
     * // Pair("prefix: ", "prefix: 42")
     * ```
     *
     * @template B
     * @param callable(static<A>):B $f Function from comonad to new value
     * @return static<B> A new comonad with the extended computation
     */
    public function extend(callable $f): Kind;
}
