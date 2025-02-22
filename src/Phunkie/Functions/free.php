<?php

namespace Phunkie\PatternMatching\Referenced {

    use Phunkie\Cats\Free\Bind;
    use Phunkie\Cats\Free\Pure;
    use Phunkie\Cats\Free\Suspend;

    /**
     * Pattern matching functions for Free monads.
     * 
     * This module provides referenced pattern matching functions for the three
     * constructors of Free monads: Pure, Suspend, and Bind.
     */

    /**
     * Pattern matches on Pure values in a Free monad.
     * 
     * Matches when a Free monad contains a pure value without any effects.
     *
     * Example:
     * ```php
     * $free = Pure(42);
     * 
     * match($free) {
     *     Pure($x) => "Got pure value: $x",
     *     default => "Not a pure value"
     * };
     * ```
     *
     * @template A
     * @param A &$value Reference to bind the pure value to
     * @return GenericReferenced Pattern matcher for Pure
     */
    function Pure(&$value)
    {
        return new GenericReferenced(Pure::class, $value);
    }

    /**
     * Pattern matches on Suspend values in a Free monad.
     * 
     * Matches when a Free monad contains a suspended effect.
     *
     * Example:
     * ```php
     * $free = Suspend(ReadLine);
     * 
     * match($free) {
     *     Suspend($effect) => "Got effect: $effect",
     *     default => "Not suspended"
     * };
     * ```
     *
     * @template F
     * @param F &$value Reference to bind the effect to
     * @return GenericReferenced Pattern matcher for Suspend
     */
    function Suspend(&$value)
    {
        return new GenericReferenced(Suspend::class, $value);
    }

    /**
     * Pattern matches on Bind operations in a Free monad.
     * 
     * Matches when a Free monad contains a bind operation that
     * sequences two computations.
     *
     * Example:
     * ```php
     * $free = Pure(1)->flatMap(fn($x) => Pure($x + 1));
     * 
     * match($free) {
     *     Bind($first, $next) => "Got bind with $first and $next",
     *     default => "Not a bind"
     * };
     * ```
     *
     * @template A,B
     * @param A &$target Reference to bind the first computation to
     * @param callable(A):B &$f Reference to bind the continuation to
     * @return GenericReferenced Pattern matcher for Bind
     */
    function Bind(&$target, &$f)
    {
        return new GenericReferenced(Bind::class, $target, $f);
    }
}
