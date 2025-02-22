<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 namespace {

    use Phunkie\Cats\State;

    /**
     * Functions for working with State monad.
     * 
     * The State monad represents computations that carry state.
     * Each computation can read and modify the state, passing it
     * along with the result to the next computation.
     */

    /**
     * Creates a State from a function.
     * 
     * Takes a function that transforms state and returns a value with new state.
     * The function is not executed until the State is run with initial state.
     *
     * Example:
     * ```php
     * // State that increments counter and returns old value
     * $counter = State(fn($s) => Pair($s, $s + 1));
     * 
     * // Run with initial state
     * $result = $counter->run(0);    // Pair(0, 1)
     * $result = $counter->run(41);   // Pair(41, 42)
     * ```
     *
     * @template S,A
     * @param callable(S):Pair<A,S> $f Function that transforms state
     * @return State<S,A> State monad
     */
    function State($a)
    {
        return new State(fn ($s) => Pair($s, $a));
    }

}

namespace Phunkie\Functions\state {

    use Phunkie\Cats\State;

    const gets = "\\Phunkie\\Functions\\state\\gets";
    /**
     * Gets the current state.
     * 
     * Creates a State that returns current state as both value and state.
     *
     * Example:
     * ```php
     * $getState = gets(fn($s) => $s + 1);
     * $result = $getState->run(42);  // Pair(42, 43)
     * ```
     *
     * @template S
     * @return State<S,S> State returning current state
     */
    function gets(callable $f): State
    {
        return new State(fn ($s) => Pair($s, $f($s)));
    }

    const get = "\\Phunkie\\Functions\\state\\get";
    /**
     * Gets the initial state.
     * 
     * Creates a State that returns an initial state as both value and state.
     *
     * Example:
     * ```php
     * $getState = get();
     * $result = $getState->run(42);  // Pair(42, 42)
     * ```
     *
     * @template S
     * @return State<S,S> State returning current state
     */
    function get(): State
    {
        return new State(fn ($s) => Pair($s, $s));
    }

    const put = "\\Phunkie\\Functions\\state\\put";
    /**
     * Sets new state.
     * 
     * Creates a State that updates state and returns unit.
     *
     * Example:
     * ```php
     * $setState = put(42);
     * $result = $setState->run(0);   // Pair(Unit, 42)
     * ```
     *
     * @template S
     * @param S $s New state value
     * @return State<S,Unit> State setting new state
     */
    function put($s): State
    {
        return new State(fn ($ignore) => Pair($s, Unit()));
    }

    const modify = "\\Phunkie\\Functions\\state\\modify";
    /**
     * Modifies state with a function.
     * 
     * Creates a State that updates state using provided function.
     *
     * Example:
     * ```php
     * $double = modify(fn($x) => $x * 2);
     * $result = $double->run(21);    // Pair(Unit, 42)
     * ```
     *
     * @template S
     * @param callable(S):S $f Function to transform state
     * @return State<S,Unit> State modifying state
     */
    function modify(callable $f): State
    {
        return new State(fn ($s) => Pair($f($s), Unit()));
    }
}
