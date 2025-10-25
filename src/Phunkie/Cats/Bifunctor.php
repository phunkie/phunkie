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
 * Represents types with two type parameters that can be mapped over independently.
 *
 * A Bifunctor is a type constructor with two type parameters that provides a way
 * to transform both independently while preserving the structure. Common examples
 * include Pair and Either.
 *
 * Bifunctors must satisfy two laws:
 * 1. Identity: bimap(id, id) == id
 * 2. Composition: bimap(f1 . f2, g1 . g2) == bimap(f1, g1) . bimap(f2, g2)
 *
 * Example:
 * ```php
 * $pair = Pair("error", 42);
 * $pair->bimap(
 *     fn($e) => "Error: $e",
 *     fn($v) => $v * 2
 * ); // Pair("Error: error", 84)
 * ```
 *
 * @template A The first type parameter
 * @template B The second type parameter
 */
interface Bifunctor extends Kind
{
    /**
     * Maps functions over both type parameters.
     *
     * Transforms both sides of the bifunctor using the provided functions.
     *
     * Example:
     * ```php
     * Pair("name", 25)->bimap(
     *     fn($s) => strtoupper($s),
     *     fn($n) => $n + 1
     * ); // Pair("NAME", 26)
     * ```
     *
     * @template C The result type for the first parameter
     * @template D The result type for the second parameter
     * @param callable(A):C $f Function to apply to the first parameter
     * @param callable(B):D $g Function to apply to the second parameter
     * @return static<C,D> A new Bifunctor with both sides transformed
     */
    public function bimap(callable $f, callable $g): Kind;

    /**
     * Maps a function over the first type parameter only.
     *
     * Equivalent to bimap($f, identity), but potentially more efficient.
     * The second parameter is left unchanged.
     *
     * Example:
     * ```php
     * Pair("name", 25)->first(fn($s) => strtoupper($s)); // Pair("NAME", 25)
     * ```
     *
     * @template C The result type for the first parameter
     * @param callable(A):C $f Function to apply to the first parameter
     * @return static<C,B> A new Bifunctor with the first side transformed
     */
    public function first(callable $f): Kind;

    /**
     * Maps a function over the second type parameter only.
     *
     * Equivalent to bimap(identity, $g), but potentially more efficient.
     * The first parameter is left unchanged.
     * For Pair, this is equivalent to the Functor map operation.
     *
     * Example:
     * ```php
     * Pair("name", 25)->second(fn($n) => $n + 1); // Pair("name", 26)
     * ```
     *
     * @template D The result type for the second parameter
     * @param callable(B):D $g Function to apply to the second parameter
     * @return static<A,D> A new Bifunctor with the second side transformed
     */
    public function second(callable $g): Kind;
}
