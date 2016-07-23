<?php

namespace Md\Phunkie\Ops\Function1;

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;

trait Function1ApplicativeOps
{
    use Function1FunctorOps;

    public function pure($a): Kind
    {
        return Function1($a);
    }

    /**
     * Function1<A,B> $this
     *
     * @param Function1<A, Function1<B,C>> $f
     * @return Function1<A,C>
     */
    public function apply(Kind $f): Kind {

        return matching($f,
            on(None)->returns(None()),
            on(Function1(_))->returns(Function1(function($x) use ($f) {
                return $f->invokeFunctionOnArg($this->invokeFunctionOnArg($x));
            })),
            on(_)->throws(new \BadMethodCallException)
        );
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }
}