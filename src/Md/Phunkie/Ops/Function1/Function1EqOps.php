<?php

namespace Md\Phunkie\Ops\Function1;

use Md\Phunkie\Algebra\Eq;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Option;

trait Function1EqOps
{
    use Eq;
    public function eqv(self $rhs, Option $arg = null): bool
    {
        if ($rhs instanceof Function1) {
            return $this->__invoke($arg->getOrElse(null)) == $rhs->__invoke($arg->getOrElse(null));
        }
        return false;
    }
}