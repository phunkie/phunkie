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

use Phunkie\Types\Pair;

/**
 * Represents computations that maintain state.
 * 
 * State<S,A> is a function that takes an initial state S and returns both:
 * - A result value of type A
 * - A new state of type S
 * 
 * It's useful for:
 * - Threading state through a computation
 * - Pure functional state manipulation
 * - Avoiding mutable variables
 *
 * Example:
 * ```php
 * // Stack operations using State
 * $push = fn($x) => new State(
 *     fn(array $stack) => Pair(
 *         array_merge($stack, [$x]), // new state
 *         Unit()                     // result
 *     )
 * );
 * 
 * $pop = new State(
 *     fn(array $stack) => Pair(
 *         array_slice($stack, 0, -1),  // new state
 *         end($stack)                  // result
 *     )
 * );
 * 
 * // Compose operations
 * $program = $push(1)
 *     ->flatMap(fn($_) => $push(2))
 *     ->flatMap(fn($_) => $pop)
 *     ->flatMap(fn($popped) => 
 *         $push($popped * 2)
 *     );
 * 
 * $result = $program->run([]); // Pair([1, 4], Unit)
 * ```
 *
 * @template S The state type
 * @template A The result type
 */
class State
{
    /**
     * @var callable(S):Pair<S,A>
     */
    private $run;

    /**
     * Creates a new State from a state transition function.
     *
     * @param callable(S):Pair<S,A> $run Function that transforms state and produces result
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    /**
     * Runs this state computation with an initial state.
     *
     * @param S $initial The initial state
     * @return Pair<S,A> A pair of final state and result
     */
    public function run($initial): Pair
    {
        return call_user_func($this->run, $initial);
    }

    /**
     * Creates a state computation that returns the state as both
     * the new state and the result value.
     *
     * @return State<S,S> A state computation where state is also the result
     */
    public function get(): State
    {
        return new State(fn($s) => Pair($s, $s));
    }

    /**
     * Gets a value derived from the current state.
     *
     * @template B
     * @param callable(S):B $f Function to apply to state
     * @return State<S,B> A state computation with transformed result
     */
    public function gets(callable $f): State
    {
        return new State(fn($s) => Pair($s, $f($s)));
    }

    /**
     * Sets the state to a new value.
     *
     * @param S $s The new state value
     * @return State<S,Unit> A state computation that updates state
     */
    public function put($s): State
    {
        return new State(fn($ignore) => Pair($s, Unit()));
    }

    /**
     * Modifies the state using a function.
     *
     * @param callable(S):S $f Function to transform state
     * @return State<S,Unit> A state computation that modifies state
     */
    public function modify(callable $f): State
    {
        return new State(fn($s) => Pair($f($s), Unit()));
    }

    /**
     * Maps a function over the result value.
     *
     * @template B
     * @param callable(A):B $f Function to transform result
     * @return State<S,B> A new state computation with transformed result
     */
    public function map(callable $f): State
    {
        return new State(function($s) use ($f) {
            $state = $this->run($s);
            return Pair($state->_1, $f($state->_2));
        });
    }

    /**
     * Chains state computations.
     *
     * @template B
     * @param callable(A):State<S,B> $f Function producing next state computation
     * @return State<S,B> The composed state computation
     */
    public function flatMap(callable $f): State
    {
        return new State(function($s) use ($f) {
            $state = $this->run($s);
            return $f($state->_2)->run($state->_1);
        });
    }
}
