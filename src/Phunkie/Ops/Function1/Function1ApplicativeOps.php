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
     * Lifts a value into the Function1 context.
     *
     * For Function1 (Reader monad), pure creates a constant function
     * if given a non-callable value, or wraps callables as Function1.
     *
     * @template T
     * @param T $a The value to lift
     * @return Function1<A,T> A Function1 wrapping the value
     */
    public function pure($a): Applicative
    {
        // If $a is callable, wrap it as Function1
        // Otherwise, create a constant function
        return is_callable($a) ? Function1($a) : Function1(fn($ignored) => $a);
    }

    /**
     * Applies a wrapped function to this function's result.
     *
     * Given:
     * - this: Function1<A,B>
     * - f: Function1<A,C>
     * Returns: Function1<A,C>
     *
     * This is function composition: applies this function then the given function.
     * Equivalent to andThen for Function1.
     *
     * @template C
     * @param Function1<A,C> $f The function to apply after this one
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
     * Applies both functions to the same input and combines their results
     * using the provided function.
     *
     * @template C
     * @template D
     * @param Kind<A,C> $fb Second function to apply
     * @param callable(B,C):D $f Function to combine results
     * @return Kind<A,D> Combined function
     * @throws TypeError If $fb is not a Function1
     */
    public function map2(Kind $fb, callable $f): Kind
    {
        if (!$fb instanceof Function1) {
            throw new \TypeError("Type error: map2 first argument must be a Function1");
        }
        $fa = $this;
        return Function1(fn($x) => $f($fa->invokeFunctionOnArg($x), $fb->invokeFunctionOnArg($x)));
    }
}
