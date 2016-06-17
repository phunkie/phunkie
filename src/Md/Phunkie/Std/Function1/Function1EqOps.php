<?php

namespace Md\Phunkie\Std\Function1;

use Md\Phunkie\Algebra\Eq;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait Function1EqOps
{
    use Eq;
    public function eqv(Kind $rhs, Option $arg): bool
    {
        switch($rhs instanceof Function1) {
            case true: return $this->__invoke($arg->getOrElse(null)) == $rhs->__invoke($arg->getOrElse(null));
            case false: return false;
        }
    }
}