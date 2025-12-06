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
 * Bifunctor operations for Pair.
 *
 * Provides operations to map functions over both elements of a Pair independently.
 *
 * @template T1 The type of the first element
 * @template T2 The type of the second element
 * @mixin \Phunkie\Types\Pair
 */
trait PairBifunctorOps
{
    /**
     * Maps functions over both elements of the Pair.
     *
     * Applies the first function to the first element and the second function
     * to the second element, producing a new Pair with both elements transformed.
     *
     * Example:
     * ```php
     * $pair = Pair("hello", 42);
     * $result = $pair->bimap(
     *     fn($s) => strtoupper($s),
     *     fn($n) => $n * 2
     * );
     * // Pair("HELLO", 84)
     * ```
     *
     * Bifunctor Laws:
     * - Identity: $pair->bimap(identity, identity) === $pair
     * - Composition: $pair->bimap(f2 . f1, g2 . g1) === $pair->bimap(f1, g1)->bimap(f2, g2)
     *
     * @template A The result type for the first element
     * @template B The result type for the second element
     * @param callable(T1):A $f Function to apply to the first element
     * @param callable(T2):B $g Function to apply to the second element
     * @return Kind|Pair<A,B> A new Pair with both elements transformed
     */
    public function bimap(callable $f, callable $g): Kind
    {
        return Pair($f($this->_1), $g($this->_2));
    }

    /**
     * Maps a function over the first element only.
     *
     * Transforms the first element while leaving the second element unchanged.
     * Equivalent to bimap($f, identity) but more explicit about intent.
     *
     * Example:
     * ```php
     * $pair = Pair("hello", 42);
     * $result = $pair->first(fn($s) => strtoupper($s));
     * // Pair("HELLO", 42)
     * ```
     *
     * @template A The result type for the first element
     * @param callable(T1):A $f Function to apply to the first element
     * @return Kind|Pair<A,T2> A new Pair with the first element transformed
     */
    public function first(callable $f): Kind
    {
        return Pair($f($this->_1), $this->_2);
    }

    /**
     * Maps a function over the second element only.
     *
     * Transforms the second element while leaving the first element unchanged.
     * Equivalent to bimap(identity, $g) and also equivalent to Functor's map.
     *
     * Example:
     * ```php
     * $pair = Pair("hello", 42);
     * $result = $pair->second(fn($n) => $n * 2);
     * // Pair("hello", 84)
     * ```
     *
     * @template B The result type for the second element
     * @param callable(T2):B $g Function to apply to the second element
     * @return Kind|Pair<T1,B> A new Pair with the second element transformed
     */
    public function second(callable $g): Kind
    {
        return Pair($this->_1, $g($this->_2));
    }
}
