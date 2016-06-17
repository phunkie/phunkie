<?php

namespace Md\Phunkie\Std\ImmList;

use Md\Phunkie\Algebra\Eq;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait ImmListEqOps
{
    use Eq;
    public function eqv(Kind $rhs, Option $args): bool
    {
        return $this == $rhs;
    }
}