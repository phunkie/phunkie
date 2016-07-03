<?php

namespace Md\Phunkie\Cats;

use Md\Phunkie\Types\Kind;

interface Monad extends FlatMap
{
    /**
     * @return Kind<TA>
     */
    public function flatten(): Kind;
}