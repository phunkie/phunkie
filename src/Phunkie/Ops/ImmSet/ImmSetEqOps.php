<?php

namespace Phunkie\Ops\ImmSet;

use Phunkie\Algebra\Eq;

trait ImmSetEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        return $this->toArray() == $rhs->toArray();
    }
}
