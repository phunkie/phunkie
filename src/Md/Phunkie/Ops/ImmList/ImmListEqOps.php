<?php

namespace Md\Phunkie\Ops\ImmList;

use Md\Phunkie\Algebra\Eq;

trait ImmListEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        return $this == $rhs;
    }
}