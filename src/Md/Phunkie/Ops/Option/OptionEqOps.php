<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Algebra\Eq;

trait OptionEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        return $this == $rhs;
    }
}