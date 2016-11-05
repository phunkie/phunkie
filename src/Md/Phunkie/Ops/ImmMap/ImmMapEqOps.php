<?php

namespace Md\Phunkie\Ops\ImmMap;

use Md\Phunkie\Algebra\Eq;
use Md\Phunkie\Types\ImmInteger;
use Md\Phunkie\Types\ImmString;

trait ImmMapEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        foreach ($this->values as $offset) {
            if ($offset instanceof ImmInteger || $offset instanceof ImmString) {
                $offset = $offset->get();
            }
            if (!$rhs->contains($offset)) {
                return false;
            }
        }
        return true;
    }
}