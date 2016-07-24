<?php

namespace Md\Phunkie\Ops\Function1;

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

        switch($f) {
            case $f == None(): return None();
            case $f instanceof Function1: return Function1(function($x) use ($f) {
                return $f->invokeFunctionOnArg($this->invokeFunctionOnArg($x));
            });
            default: throw new \BadMethodCallException();
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }
}