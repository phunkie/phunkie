<?php

namespace Md\Phunkie\Ops;

use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Types\Kind;

trait FunctorOps
{
    /**
     * @param Function1<A,B> $f
     * @return Function1<Kind<F,A>, Kind<F,B>>
     */
    public function lift($f): callable { return function (Functor $fa) use ($f) { return $fa->map($f); }; }

    /**
     * @param B $b
     * @return Kind<B>
     */
    public function as($b): Kind { return $this->map(function($ignored) use ($b) { return $b; }); }

    /**
     * @return Kind<Unit>
     */
    public function void(): Kind { return $this->map(function($ignored) { return Unit(); }); }

    public function zipWith($f): Kind { return $this->map(function($a) use ($f) { return Pair($a, $f($a)); }); }
}