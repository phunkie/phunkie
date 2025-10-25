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
 * Monad operations for Function1.
 *
 * This trait implements the Monad interface for functions, providing:
 * - flatMap: Kleisli composition - chains functions that return wrapped values
 * - flatten: Removes one level of nesting from functions returning functions
 *
 * Monad Laws:
 * - Left Identity: pure(a)->flatMap(f) === f(a)
 * - Right Identity: m->flatMap(pure) === m
 * - Associativity: m->flatMap(f)->flatMap(g) === m->flatMap(fn($x) => f($x)->flatMap(g))
 *
 * Example:
 * ```php
 * // flatMap enables Kleisli composition
 * $f = Function1(fn($x) => Function1(fn($y) => $x + $y));
 * $g = $f->flatMap(fn($h) => Function1(fn($z) => $h($z) * 2));
 * $g(3)(5); // ((3 + 5) * 2) = 16
 *
 * // flatten removes one level of function nesting
 * $nested = Function1(fn($x) => Function1(fn($y) => $x + $y));
 * $flat = $nested->flatten();
 * $flat(3)(5); // 8
 * ```
 *
 * @template A The input type
 * @template B The output type
 * @mixin Function1
 */
trait Function1MonadOps
{
    use Function1FunctorOps;

    /**
     * Chains computations that produce Function1 values (Kleisli composition).
     *
     * Given:
     * - this: Function1<A,B>
     * - f: callable(B):Function1<A,C>
     * Returns: Function1<A,C>
     *
     * This enables composing functions where the transformation function itself
     * produces a Function1. The result is a new function that, when given an input,
     * applies this function to it, passes the result to f, and then applies the
     * resulting function to the same original input.
     *
     * Example:
     * ```php
     * // Create a function that adds x to its input
     * $addX = fn($x) => Function1(fn($y) => $x + $y);
     *
     * // Create a base function that doubles
     * $double = Function1(fn($x) => $x * 2);
     *
     * // Compose: double the input, then create an adder with that result
     * $composed = $double->flatMap($addX);
     * $composed(3); // Returns a Function1 that adds 6 to its input
     * ```
     *
     * @template C The result output type
     * @param callable(B):Function1<A,C> $f Function returning a Function1
     * @return Function1<A,C> The composed function
     */
    public function flatMap(callable $f): Kind
    {
        $self = $this;
        return Function1(function ($x) use ($self, $f) {
            $result = $f($self->invokeFunctionOnArg($x));
            if (!$result instanceof Function1) {
                throw new \TypeError("flatMap callback must return a Function1");
            }
            return $result->invokeFunctionOnArg($x);
        });
    }

    /**
     * Flattens a nested Function1 structure.
     *
     * Given:
     * - this: Function1<A,Function1<A,B>>
     * Returns: Function1<A,B>
     *
     * Removes one level of Function1 nesting. When you have a function that
     * returns another function, flatten combines them into a single function
     * that applies both transformations.
     *
     * Example:
     * ```php
     * // A function that returns a function
     * $nested = Function1(fn($x) => Function1(fn($y) => $x + $y));
     *
     * // Flatten it
     * $flat = $nested->flatten();
     *
     * // The flattened function applies the input to both layers
     * $flat(5); // Returns a Function1(fn($y) => 5 + $y)
     *
     * // Or with immediate application:
     * $result = $flat(5)->run(3); // 5 + 3 = 8
     * ```
     *
     * @return Function1<A,B> The flattened function
     */
    public function flatten(): Kind
    {
        return $this->flatMap(fn($x) => $x);
    }
}
