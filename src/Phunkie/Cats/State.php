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
 * Class State<S,A>
 */
class State
{
    /**
     * @var callable<S, Pair<S,A>>
     */
    private $run;

    /**
     * @param callable<S, Pair<S,A>> $run
     */
    public function __construct(callable $run)
    {
        $this->run = $run;
    }

    /**
     * @param $initial
     * @return Pair<S,A>
     */
    public function run($initial): Pair
    {
        return call_user_func($this->run, $initial);
    }

    /**
     * @return State<S,S>
     */
    public function get()
    {
        return new State(function ($s) {
            return \Pair($s, $s);
        });
    }

    /**
     * @param callable<S,A> $f
     * @return State<S,A>
     */
    public function gets(callable $f): State
    {
        return new State(function ($s) use ($f) {
            return \Pair($s, $f($s));
        });
    }

    /**
     * @param S $s
     * @return State<S,Unit>
     */
    public function put($s): State
    {
        return new State(function ($ignore) use ($s) {
            return \Pair($s, Unit());
        });
    }

    /**
     * @param callable<S,S> $f
     * @return State<S,Unit>
     */
    public function modify(callable $f)
    {
        return new State(function ($s) use ($f) {
            return \Pair($f($s), Unit());
        });
    }

    /**
     * @param callable<A,B> $f
     * @return State<S,B>
     */
    public function map(callable $f): State
    {
        return new State(function ($s) use ($f) {
            $state = $this->run($s);
            return \Pair($state->_1, $f($state->_2));
        });
    }

    /**
     * @param callable<A,State<S,B>> $f
     * @return State<S,B>
     */
    public function flatMap(callable $f): State
    {
        return new State(function ($s) use ($f) {
            $state = $this->run($s);
            return $f($state->_2)->run($state->_1);
        });
    }
}
