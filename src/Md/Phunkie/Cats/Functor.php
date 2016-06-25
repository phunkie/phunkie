<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Cats\Functor\Invariant;
use Md\Phunkie\Types\Kind;

/**
 * Functor<Kind<F,A>>
 */
interface Functor extends Invariant
{
    /**
     * @param Function1<A,B> $f
     * @return Kind<F,B>
     */
    public function map(callable $f): Kind;

    /**
     * @param Function1<A,B> $f
     * @return Function1<Kind<F,A>, Kind<F,B>>
     */
    public function lift($f): callable;

    /**
     * @param B $b
     * @return Kind<B>
     */
    public function as($b): Kind;

    /**
     * @param B $b
     * @return Kind<Unit>
     */
    public function void($b): Kind;

    public function zipWith($f): Kind;
}