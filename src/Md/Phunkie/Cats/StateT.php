<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

/**
 * StateT<F,S,A>
 */
class StateT
{
    /**
     * @var Monad<State<S,A>>
     */
    private $initial;

    public function __construct(Kind $state)
    {
        $this->initial = $state;
    }

    /**
     * @param callable<A,B> $f
     * @return StateT<F,S,B>
     */
    public function map($f)
    {
        return new StateT($this->initial->map(function($s) use ($f) {
            return $f($this->run($s));
        }));
    }

    public function run($initial)
    {
        return $this->initial->flatMap(function($f) use ($initial) { return $f($initial); });
    }
}
