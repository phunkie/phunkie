<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Pair;

use Phunkie\Types\Kind;

/**
 * Comonad operations for Pair.
 *
 * Pair is a natural Comonad where the first element provides context and
 * the second element is the focus value. The comonad operations allow
 * computations that depend on this context.
 *
 * @template T1 The type of the first element (context)
 * @template T2 The type of the second element (focus value)
 * @mixin \Phunkie\Types\Pair
 */
trait PairComonadOps
{
    /**
     * Extracts the focus value from the Pair.
     *
     * Returns the second element, which is considered the "value" of the Pair
     * while the first element provides context. This is the dual of Monad's
     * pure/return operation.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $pair->extract();  // 42
     * ```
     *
     * Comonad Law (Extract/Duplicate):
     * - $pair->duplicate()->extract() === $pair
     *
     * @return T2 The second element
     */
    public function extract()
    {
        return $this->_2;
    }

    /**
     * Wraps the Pair in another layer of Pair with the same context.
     *
     * Creates a nested Pair where the outer Pair has the same first element
     * (context) and the inner Pair is the original. This creates a "Pair of Pair"
     * structure.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $pair->duplicate();
     * // Pair("label", Pair("label", 42))
     * ```
     *
     * Comonad Laws:
     * - $pair->duplicate()->extract() === $pair
     * - $pair->duplicate()->map(fn($p) => $p->extract()) === $pair
     *
     * @return Kind|Pair<T1,Pair<T1,T2>> A nested Pair structure
     */
    public function duplicate(): Kind
    {
        return Pair($this->_1, $this);
    }

    /**
     * Extends a comonadic computation over the Pair.
     *
     * Applies a function that operates on the entire Pair to produce a new
     * value. The function receives the whole Pair (with its context) and can
     * use both elements to compute the result. This is the dual of Monad's
     * flatMap/bind.
     *
     * Example:
     * ```php
     * $pair = Pair("prefix: ", 42);
     * $pair->extend(fn($p) => $p->_1 . $p->extract());
     * // Pair("prefix: ", "prefix: 42")
     *
     * $pair = Pair(10, 5);
     * $pair->extend(fn($p) => $p->_1 + $p->_2);
     * // Pair(10, 15)
     * ```
     *
     * Comonad Laws:
     * - $pair->extend(fn($p) => $p->extract()) === $pair
     * - $pair->extend($f)->extract() === $f($pair)
     *
     * @template B The result type
     * @param callable(Pair<T1,T2>):B $f Function from Pair to new value
     * @return Kind|Pair<T1,B> A new Pair with extended computation result
     */
    public function extend(callable $f): Kind
    {
        return Pair($this->_1, $f($this));
    }

    /**
     * Alias for extend that may be more familiar to some users.
     *
     * This is the same as extend but with a name that emphasizes the
     * comonadic flatMap-like behavior.
     *
     * @template B The result type
     * @param callable(Pair<T1,T2>):B $f Function from Pair to new value
     * @return Kind|Pair<T1,B>
     */
    public function coflatMap(callable $f): Kind
    {
        return $this->extend($f);
    }
}
