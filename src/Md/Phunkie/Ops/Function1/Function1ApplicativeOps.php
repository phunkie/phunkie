<?php

namespace Md\Phunkie\Ops\Function1;

use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\None;

trait Function1ApplicativeOps
{
    use Function1FunctorOps;
    public function pure(callable $a): Kind
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
        switch (true) {
            case ($f instanceof None): return None(); break;
            case (!$f instanceof Function1):
                throw new \BadMethodCallException;
            case ($f instanceof Function1):
                return $this->andThen($f);
        }
    }
}