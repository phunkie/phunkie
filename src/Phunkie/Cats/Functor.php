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

use Phunkie\Cats\Functor\Invariant;
use Phunkie\Types\Kind;

/**
 * Represents types that can be mapped over.
 * 
 * A Functor is a type constructor that provides a way to transform its contents
 * while preserving its structure. All functors must satisfy two laws:
 * 1. Identity: fa.map(x => x) == fa
 * 2. Composition: fa.map(f).map(g) == fa.map(x => g(f(x)))
 *
 * Example:
 * ```php
 * $option = Some(42);
 * $option->map(fn($x) => $x * 2); // Some(84)
 * 
 * $list = ImmList(1, 2, 3);
 * $list->map(fn($x) => $x + 1);   // List(2, 3, 4)
 * ```
 *
 * @template A
 */
interface Functor extends Invariant
{
    /**
     * Applies a function to the contents of this Functor.
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return static<B> A new Functor containing the mapped values
     */
    public function map(callable $f): Kind;

    /**
     * Lifts a function to operate on Functors.
     * 
     * Converts a function from A => B to F<A> => F<B>.
     *
     * Example:
     * ```php
     * $f = fn($x) => $x * 2;
     * $lifted = $functor->lift($f);
     * $lifted(Some(42)); // Some(84)
     * ```
     *
     * @template B
     * @param callable(A):B $f The function to lift
     * @return callable(static<A>):static<B> The lifted function
     */
    public function lift($f): callable;

    /**
     * Replaces the contents of this Functor with a constant value.
     * 
     * Equivalent to map(_ => $b) but potentially more efficient.
     *
     * Example:
     * ```php
     * $list = ImmList(1, 2, 3);
     * $list->as("x"); // List("x", "x", "x")
     * ```
     *
     * @template B
     * @param B $b The value to replace with
     * @return static<B> A new Functor containing only $b
     */
    public function as($b): Kind;

    /**
     * Discards the contents of this Functor, replacing them with Unit.
     * 
     * Equivalent to as(Unit()) but potentially more efficient.
     *
     * Example:
     * ```php
     * $option = Some(42);
     * $option->void(); // Some(Unit())
     * ```
     *
     * @return static<Unit> A new Functor containing Unit
     */
    public function void(): Kind;

    /**
     * Maps a function over this Functor and pairs the results with original values.
     *
     * Example:
     * ```php
     * $list = ImmList(1, 2, 3);
     * $list->zipWith(fn($x) => $x * 2); // List(Pair(1,2), Pair(2,4), Pair(3,6))
     * ```
     *
     * @template B
     * @param callable(A):B $f The function to apply
     * @return static<Pair<A,B>> A new Functor containing pairs of original and mapped values
     */
    public function zipWith($f): Kind;

    /**
     * Invariant functor mapping.
     * 
     * Maps a pair of functions over this functor, one in each direction.
     * Must satisfy: imap(f, g).imap(h, i) == imap(x => h(f(x)), x => g(i(x)))
     *
     * @template B
     * @param callable(A):B $f The forward function
     * @param callable(B):A $g The reverse function
     * @return static<B> A new Functor
     */
    public function imap(callable $f, callable $g): Kind;
}
