<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

interface Applicative extends Apply
{
    /**
     * @param T $a
     * @return Kind<T>
     */
    public function pure($a): Kind;
}