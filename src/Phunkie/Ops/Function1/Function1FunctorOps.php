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

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;

/**
 * Functor operations for Function1.
 * 
 * This trait implements the Functor interface for functions, allowing function
 * composition through map and imap operations. It enables transforming the
 * output of functions while preserving their input behavior.
 *
 * Example:
 * ```php
 * $f = Function1(fn($x) => $x + 1);
 * 
 * // map composes a new function with the output
 * $g = $f->map(fn($x) => $x * 2);
 * $g(3); // (3 + 1) * 2 = 8
 * 
 * // imap is the same as map for functions
 * $h = $f->imap(
 *     fn($x) => $x * 2,  // forward
 *     fn($x) => $x / 2   // backward (unused)
 * );
 * $h(3); // (3 + 1) * 2 = 8
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @mixin \Phunkie\Types\Function1
 */
trait Function1FunctorOps
{
    use FunctorOps;

    /**
     * Maps over the output of this function.
     * 
     * Creates a new function that applies this function first,
     * then applies the mapping function to the result.
     *
     * @template C
     * @param callable(B):C $f The function to compose with
     * @return Kind<A,C> The composed function
     */
    public function map(callable $f): Kind
    {
        return $this->andThen($f);
    }

    /**
     * Maps over the output of this function (same as map).
     * 
     * For Function1, imap behaves the same as map, ignoring the
     * backward transformation since functions are only mapped forward.
     *
     * @template C
     * @param callable(B):C $f Forward transformation
     * @param callable(C):B $g Backward transformation (unused)
     * @return Kind<A,C> The composed function
     */
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }
}
