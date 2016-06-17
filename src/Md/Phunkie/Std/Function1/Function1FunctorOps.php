<?php

namespace Md\Phunkie\Std\Function1;

use Md\Phunkie\Cats\Functor;
use Md\Phunkie\Types\Kind;

/**
 * @mixin \Md\Phunkie\Types\Function1
 */
trait Function1FunctorOps
{
    use Functor;
    public function map(callable $f): Kind
    {
        return $this->andThen($f);
    }
}