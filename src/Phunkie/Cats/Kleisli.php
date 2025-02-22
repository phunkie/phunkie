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

use function Phunkie\Functions\kleisli\kleisli as k;

/**
 * Represents a function that returns a monadic value.
 * 
 * Kleisli arrows are functions of type A => M<B> where M is a monad. They compose
 * functions that return values wrapped in a monadic context, allowing for clean
 * sequencing of computations that might fail, have side effects, or carry extra context.
 *
 * Laws:
 * 1. Left identity: pure(a).andThen(f) == f(a)
 * 2. Right identity: f.andThen(pure) == f
 * 3. Associativity: (f.andThen(g)).andThen(h) == f.andThen(g.andThen(h))
 *
 * Example:
 * ```php
 * // Functions that might fail
 * $parseInt = Kleisli(fn($s) => 
 *     is_numeric($s) ? Some(intval($s)) : None()
 * );
 * $double = Kleisli(fn($i) => Some($i * 2));
 * 
 * // Compose them
 * $parseAndDouble = $parseInt->andThen($double);
 * 
 * $parseAndDouble("123");  // Some(246)
 * $parseAndDouble("abc");  // None
 * ```
 *
 * @template A Input type
 * @template B Output type
 * @template M The monad type constructor
 */
class Kleisli
{
    private $run;

    /**
     * Creates a new Kleisli arrow.
     *
     * @param callable(A):M<B> $run The function returning a monadic value
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    /**
     * Applies this Kleisli arrow to a value.
     *
     * @param A $a The input value
     * @return M<B> The result in the monadic context
     */
    public function run($a)
    {
        return call_user_func($this->run, $a);
    }

    /**
     * Composes this Kleisli arrow with another.
     * 
     * The result is a new Kleisli arrow that first applies this function
     * and then applies the given function to the result.
     *
     * @template C
     * @param Kleisli<B,C,M> $g The Kleisli arrow to compose with
     * @return Kleisli<A,C,M> The composed Kleisli arrow
     */
    public function andThen(Kleisli $g): Kleisli
    {
        return new Kleisli(fn($a) => $this->run($a)->flatMap(fn($b) => $g->run($b)));
    }

    /**
     * Composes this Kleisli arrow with another in reverse order.
     *
     * @template C
     * @param Kleisli<C,A,M> $g The Kleisli arrow to compose with
     * @return Kleisli<C,B,M> The composed Kleisli arrow
     */
    public function compose(Kleisli $g): Kleisli
    {
        return $g->andThen($this);
    }
}
