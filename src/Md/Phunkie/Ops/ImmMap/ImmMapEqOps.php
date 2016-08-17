<?php

namespace Md\Phunkie\Ops\ImmMap;

use Md\Phunkie\Algebra\Eq;

trait ImmMapEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        foreach ($this->values as $offset) {
            if (!$rhs->contains($offset)) {
                return false;
            }
        }
        return true;
    }
}