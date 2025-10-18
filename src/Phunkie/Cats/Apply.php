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
 * Represents functors that can apply functions wrapped in the functor context.
 * 
 * Apply extends Functor with the ability to apply functions that are themselves
 * wrapped in the functor context. This enables combining multiple functors using
 * multi-parameter functions.
 *
 * Laws:
 * 1. Associative composition: fa.apply(fb.apply(fc.map(f => g => x => f(g(x))))) == 
 *                            fa.apply(fb).apply(fc)
 *
 * Example:
 * ```php
 * $f = Some(fn($x) => $x * 2);
 * $v = Some(21);
 * $v->apply($f);  // Some(42)
 * 
 * // Combining multiple values
 * $v1 = Some(2);
 * $v2 = Some(3);
 * $v1->map2($v2, fn($a, $b) => $a * $b);  // Some(6)
 * ```
 *
 * @template A
 * @extends Functor<A>
 */
interface Apply extends Functor
{
    /**
     * Applies a wrapped function to this value.
     *
     * @template B
     * @param Kind<static,callable(A):B> $f The wrapped function
     * @return Kind<B> The result of applying the function
     */
    public function apply(Kind $f): Kind;

    /**
     * Maps a binary function over two values in the functor context.
     *
     * @template B
     * @template C
     * @param Kind<static,B> $fb Second value
     * @param callable(A,B):C $f Function to apply
     * @return Kind<C> Result of applying the function
     */
    public function map2(Kind $fb, callable $f): Kind;
}
