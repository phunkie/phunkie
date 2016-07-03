<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

interface Apply extends Functor
{
    /**
     * @param Kind<callable<A,B>> $f
     * @return Kind<B>
     */
    public function apply(Kind $f): Kind;

    /**
     * @param Kind<B> $fb
     * @param (A,B) => C $f
     * @return Kind<C>
     */
    public function map2(Kind $fb, callable $f): Kind;
}