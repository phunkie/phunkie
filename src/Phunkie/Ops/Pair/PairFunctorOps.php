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

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;

/**
 * Functor operations for Pair.
 *
 * Provides map operations that transform the second element of a Pair while
 * keeping the first element unchanged. This makes Pair a functor in its
 * second type parameter.
 *
 * @template T1 The type of the first element (context)
 * @template T2 The type of the second element (value)
 * @mixin \Phunkie\Types\Pair
 */
trait PairFunctorOps
{
    use FunctorOps;

    /**
     * Maps a function over the second element of the Pair.
     *
     * Transforms the second element (the "value") while keeping the first
     * element (the "context") unchanged. This is equivalent to calling
     * second($f) from Bifunctor operations.
     *
     * Example:
     * ```php
     * $pair = Pair("label", 42);
     * $result = $pair->map(fn($n) => $n * 2);
     * // Pair("label", 84)
     * ```
     *
     * Functor Laws:
     * - Identity: $pair->map(fn($x) => $x) === $pair
     * - Composition: $pair->map(fn($x) => g(f($x))) === $pair->map($f)->map($g)
     *
     * @template B The result type
     * @param callable(T2):B $f Function to apply to the second element
     * @return Kind|Pair<T1,B> A new Pair with the second element transformed
     */
    public function map(callable $f): Kind
    {
        return Pair($this->_1, $f($this->_2));
    }

    /**
     * Invariant functor map.
     *
     * For Pair, this is equivalent to map since Pair is covariant in its
     * second type parameter. The reverse function $g is unused.
     *
     * @template B The result type
     * @param callable(T2):B $f The forward mapping function
     * @param callable(B):T2 $g The reverse mapping function (unused)
     * @return Kind|Pair<T1,B>
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }
}
