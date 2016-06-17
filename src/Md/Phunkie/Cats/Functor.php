<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

/**
 * Functor<Kind<F,A>>
 */
trait Functor
{
    /**
     * @param Function1<A,B> $f
     * @return Kind<F,B>
     */
    abstract public function map(callable $f): Kind;

    /**
     * @param Function1<A,B> $f
     * @return Function1<Kind<F,A>, Kind<F,B>>
     */
    public function lift($f): callable { return function (Functor $fa) use ($f) { return $fa->map($f); }; }

    /**
     * @param B $b
     * @return Kind<B>
     */
    public function as($b) { return $this->map(function($ignored) use ($b) { return $b; }); }

    /**
     * @param B $b
     * @return Kind<Unit>
     */
    public function void($b) { return $this->map(function($ignored) use ($b) { return Unit(); }); }
}