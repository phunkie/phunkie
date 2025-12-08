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
 * StateT allows you to work with nested structures of the form S => F[(S, A)].
 * This lets you combine stateful computations with other effects like Option, List, or Validation.
 *
 * @template F The outer monad
 * @template S The state type
 * @template A The result type
 * @implements Kind<StateT,A>
 */
class StateT implements Kind
{
    public const kind = "StateT";

    /**
     * @var callable(S): Kind<F, Pair<S, A>>
     */
    private $run;

    /**
     * Creates a new StateT.
     * 
     * Can be constructed with:
     * 1. A callable(S): Kind<F, Pair<S, A>> (New style)
     * 2. A Kind<F, State<S, A>> (Legacy style)
     *
     * @param callable|Kind $value
     */
    public function __construct($value)
    {
        if ($value instanceof Kind) {
            // Adapt Legacy F[State] to S => F[Pair]
            $this->run = fn($s) => $value->map(fn(State $state) => $state->run($s));
        } else {
            $this->run = $value;
        }
    }

    /**
     * Runs the state transition.
     *
     * @param S $s Initial state
     * @return Kind<F, Pair<S, A>>
     */
    public function run($s)
    {
        return ($this->run)($s);
    }

    /**
     * Maps a function over the result values in the state computation.
     *
     * @template B
     * @param callable(A):B $f The function to apply to result values
     * @return StateT<F,S,B> A new StateT with transformed results
     */
    public function map(callable $f): StateT
    {
        return new StateT(
            fn($s) =>
            $this->run($s)->map(
                fn(Pair $p) =>
                Pair($p->_1, $f($p->_2))
            )
        );
    }

    /**
     * Chains StateT computations.
     *
     * @template B
     * @param callable(A):StateT<F,S,B> $f Function producing next computation
     * @return StateT<F,S,B> The composed computation
     */
    public function flatMap(callable $f): StateT
    {
        return new StateT(function($s) use ($f) {
            return $this->run($s)->flatMap(function(Pair $p) use ($f) {
                // p is Pair(s', a)
                $nextStateT = $f($p->_2);
                return $nextStateT->run($p->_1);
            });
        });
    }

    public function getTypeArity(): int
    {
        return 3;
    }

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
            fn($s) =>
            $this->run($s)->map(
                fn(Pair $p) =>
                Pair($f($p->_1), \Unit())
            )
        );
    }
}
