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
}