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

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Functor;
use Phunkie\Types\Function1;
use Phunkie\Types\Kind;
use TypeError;

/**
 * Applicative functor operations for Function1.
 * 
 * This trait implements the Applicative interface for functions, allowing:
 * - Lifting values into the function context (pure)
 * - Applying functions wrapped in Function1 context (apply)
 * - Combining multiple Function1s (map2)
 *
 * Example:
 * ```php
 * $f = Function1(fn($x) => $x + 1);
 * $g = Function1(fn($x) => $x * 2);
 * 
 * // pure lifts a value into a constant function
 * $const3 = $f->pure(3); // Function1(fn($x) => 3)
 * 
 * // apply combines functions
 * $h = $f->apply($g); // Function1(fn($x) => $g($f($x)))
 * 
 * // map2 combines results of two functions
 * $sum = $f->map2($g, fn($a, $b) => $a + $b);
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @mixin Function1
 */
trait Function1ApplicativeOps
{
    use Function1FunctorOps;

    /**
     * Lifts a value into a constant function.
     * 
     * Creates a function that ignores its input and always returns
     * the provided value.
     *
     * @template T
     * @param T $a The value to lift
     * @return Function1<A,T> A constant function returning $a
     */
    public function pure($a): Applicative
    {
        return Function1($a);
    }

    /**
     * Applies a wrapped function to this function's result.
     * 
     * Given:
     * - this: Function1<A,B>
     * - f: Function1<A,B->C>
     * Returns: Function1<A,C>
     *
     * @template C
     * @param Function1<A,callable(B):C> $f The function to apply
     * @return Function1<A,C> The composed function
     */
    public function apply(Kind $f): Kind { return match (true) {
        $f == None() => None(),
        $f instanceof Function1 => Function1(fn ($x) => $f->invokeFunctionOnArg($this->invokeFunctionOnArg($x))),
        default => throw new \BadMethodCallException()};
    }

    /**
     * Maps two functions into a combined result.
     * 
     * Applies both functions to the input and combines their results
     * using the provided function.
     *
     * @template C
     * @template D
     * @param Kind<A,C> $fb Second function to apply
     * @param callable(B,C):D $f Function to combine results
     * @return Kind<A,D> Combined function
     * @throws TypeError If $fb is not a Functor
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        if (!$fb instanceof Functor) {
            throw new \TypeError("Type error: map2 first argument must be a Functor");
        }
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
