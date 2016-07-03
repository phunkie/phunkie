<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

interface FlatMap extends Functor
{
    /**
     * @param (TA) => Kind<TB> $f
     * @return Kind<TB>
     */
    public function flatMap(callable $f): Kind;
}