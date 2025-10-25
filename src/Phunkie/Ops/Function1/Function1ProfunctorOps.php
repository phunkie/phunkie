<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Function1;

use Phunkie\Types\Function1;
use Phunkie\Types\Kind;

/**
 * Profunctor operations for Function1.
 *
 * A Profunctor is contravariant in its first argument and covariant in its second.
 * For Function1<A,B>, this means:
 * - We can map covariantly over the output (B) - that's regular map/rmap
 * - We can map contravariantly over the input (A) - that's lmap
 * - We can do both simultaneously - that's dimap
 *
 * Profunctor Laws:
 * - Identity: dimap(id, id) === id
 * - Composition: dimap(f . g, h . i) === dimap(g, h) . dimap(f, i)
 * - lmap(f) === dimap(f, id)
 * - rmap(g) === dimap(id, g)
 *
 * Example:
 * ```php
 * $f = Function1(fn(int $x): int => $x * 2);
 *
 * // lmap: preprocess input (contravariant)
 * $g = $f->lmap(fn(string $s) => (int)$s);
 * $g("21"); // "21" -> 21 -> 42
 *
 * // rmap: postprocess output (covariant, same as map)
 * $h = $f->rmap(fn(int $x) => (string)$x);
 * $h(21); // 21 -> 42 -> "42"
 *
 * // dimap: both at once
 * $i = $f->dimap(
 *     fn(string $s) => (int)$s,  // preprocess input
 *     fn(int $x) => (string)$x   // postprocess output
 * );
 * $i("21"); // "21" -> 21 -> 42 -> "42"
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @mixin Function1
 */
trait Function1ProfunctorOps
{
    /**
     * Maps over both input and output.
     *
     * Given:
     * - this: Function1<A,B>
     * - ab: callable(C):A (contravariant - preprocesses input)
     * - cd: callable(B):D (covariant - postprocesses output)
     * Returns: Function1<C,D>
     *
     * Creates a new function that:
     * 1. Applies ab to preprocess the input from C to A
     * 2. Applies this function to transform A to B
     * 3. Applies cd to postprocess the output from B to D
     *
     * Example:
     * ```php
     * // Convert Celsius to Fahrenheit
     * $celsiusToF = Function1(fn($c) => $c * 9/5 + 32);
     *
     * // Add parsing input and formatting output
     * $stringCtoFString = $celsiusToF->dimap(
     *     fn($s) => (float)$s,           // Parse string to float
     *     fn($f) => number_format($f, 1) // Format to 1 decimal
     * );
     *
     * $stringCtoFString("100"); // "212.0"
     * ```
     *
     * @template C The new input type
     * @template D The new output type
     * @param callable(C):A $ab Function to preprocess input
     * @param callable(B):D $cd Function to postprocess output
     * @return Function1<C,D> The new function with both transformations
     */
    public function dimap(callable $ab, callable $cd): Function1
    {
        return $this->lmap($ab)->rmap($cd);
    }

    /**
     * Maps contravariantly over the input (preprocessing).
     *
     * Given:
     * - this: Function1<A,B>
     * - f: callable(C):A
     * Returns: Function1<C,B>
     *
     * Creates a new function that preprocesses its input using f before
     * applying this function. This is contravariant because we're transforming
     * in the opposite direction - from the new input type to the original.
     *
     * Example:
     * ```php
     * // A function that works on integers
     * $double = Function1(fn(int $x): int => $x * 2);
     *
     * // Extend it to work on strings by preprocessing
     * $doubleString = $double->lmap(fn(string $s) => (int)$s);
     *
     * $doubleString("21"); // "21" -> 21 -> 42
     * ```
     *
     * @template C The new input type
     * @param callable(C):A $f Function to preprocess input
     * @return Function1<C,B> Function accepting the new input type
     */
    public function lmap(callable $f): Function1
    {
        return $this->compose($f);
    }

    /**
     * Maps covariantly over the output (postprocessing).
     *
     * Given:
     * - this: Function1<A,B>
     * - g: callable(B):C
     * Returns: Function1<A,C>
     *
     * This is just an alias for map/andThen - it postprocesses the output.
     * Included for Profunctor completeness and symmetry with lmap.
     *
     * Example:
     * ```php
     * // A function that works on integers
     * $double = Function1(fn(int $x): int => $x * 2);
     *
     * // Transform output to string
     * $doubleToString = $double->rmap(fn(int $x) => (string)$x);
     *
     * $doubleToString(21); // 21 -> 42 -> "42"
     * ```
     *
     * @template C The new output type
     * @param callable(B):C $g Function to postprocess output
     * @return Function1<A,C> Function producing the new output type
     */
    public function rmap(callable $g): Function1
    {
        return $this->andThen($g);
    }
}
