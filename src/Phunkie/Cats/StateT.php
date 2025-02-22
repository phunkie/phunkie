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
use Phunkie\Types\Pair;

/**
 * A monad transformer that combines State with another monad F.
 * 
 * StateT allows you to work with nested structures of the form F<State<S,A>>
 * by lifting state operations into any monad. This lets you combine stateful
 * computations with other effects like Option, List, or IO.
 *
 * Example:
 * ```php
 * // Counter with Option effect
 * $state = ImmList(
 *     State(fn($s) => Pair($s + 1, $s)),
 *     State(fn($s) => $s < 3 ? Pair($s + 1, $s) : None())
 * );
 * 
 * $st = new StateT($state);
 * $result = $st
 *     ->map(fn($x) => $x * 2)
 *     ->run(0);
 * // ImmList(Pair(1, 0), Pair(1, 0))
 * ```
 *
 * @template F The outer monad
 * @template S The state type
 * @template A The result type
 * @implements Kind<StateT,A>
 */
class StateT implements Kind
{
    const kind = "StateT";

    /**
     * @var Kind<F,State<S,A>>
     */
    private $monad;

    /**
     * Creates a new StateT wrapping a monadic value containing States.
     *
     * @param Kind<F,State<S,A>> $value The wrapped monadic value
     */
    public function __construct(Kind $monad)
    {
        $this->monad = $monad;
    }

    /**
     * Maps a function over the result values in the state computation.
     * 
     * This lifts a function A => B into the StateT context, transforming
     * StateT<F,S,A> into StateT<F,S,B>. The state itself remains unchanged,
     * only the result values are modified.
     *
     * Example:
     * ```php
     * $state = ImmList(
     *     State(fn($s) => Pair($s, $s + 1))
     * );
     * 
     * $st = new StateT($state);
     * $result = $st
     *     ->map(fn($x) => $x * 2)
     *     ->run(1);
     * // ImmList(Pair(1, 4))  // State unchanged (1), result doubled (2 * 2)
     * ```
     *
     * @template B
     * @param callable(A):B $f The function to apply to result values
     * @return StateT<F,S,B> A new StateT with transformed results
     */
    public function map(callable $f): StateT
    {
        return new StateT($this->monad->map(fn(State $s) => $s->gets($f)));
    }

    /**
     * Runs this stateful computation with an initial state.
     *
     * @param S $s The initial state
     * @return Kind<F,Pair<S,A>> The result in the outer monad
     */
    public function run($s)
    {
        return $this->monad->map(fn (State $state) => $state->run($s));
    }

    /**
     * Chains StateT computations.
     * 
     * This allows composing stateful computations where each step can depend
     * on the result of the previous step. The state is threaded through the
     * computation chain.
     *
     * Example:
     * ```php
     * $increment = new StateT(ImmList(
     *     State(fn($s) => Pair($s + 1, $s))
     * ));
     * 
     * $double = fn($x) => new StateT(ImmList(
     *     State(fn($s) => Pair($s, $x * 2))
     * ));
     * 
     * $result = $increment
     *     ->flatMap($double)
     *     ->run(1);
     * // ImmList(Pair(2, 2))  // State incremented (1->2), value doubled (1*2)
     * ```
     *
     * @template B
     * @param callable(A):StateT<F,S,B> $f Function producing next computation
     * @return StateT<F,S,B> The composed computation
     */
    public function flatMap(callable $f): StateT
    {
        return new StateT(
            $this->monad->flatMap(
                fn(State $state) =>
                    $this->monad->pure($state->flatMap(function($a) use ($f) {
                        $x = null;
                        $f($a)->monad->map(function ($_) use(&$x) {
                            return $x = $_;
                        });                        
                        return $x;
                    }))
            )
        );
    }

    /**
     * Returns the number of type parameters.
     *
     * @return int Always returns 3 (F, S, and A)
     */
    public function getTypeArity(): int
    {
        return 3;
    }

    /**
     * Returns the type variables for this Kind.
     *
     * @return array<string> Array containing the type variables
     */
    public function getTypeVariables(): array
    {
        return ['F', 'S', 'A'];
    }

    /**
     * Modifies the state using a function.
     *
     * @param callable(S):S $f Function to transform state
     * @return StateT<F,S,Unit>
     */
    public function modify(callable $f): StateT
    {
        return new StateT(
            $this->monad->map(fn(State $state) =>
                $state->modify($f)
            )
        );
    }
}
