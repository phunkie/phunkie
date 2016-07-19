<?php

namespace Md\Phunkie\Ops\ImmList;

use Md\Phunkie\Algebra\Eq;
use function Md\Phunkie\Functions\show\show;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait ImmListEqOps
{
    use Eq;
    public function eqv(Kind $rhs): bool
    {
        return $this == $rhs;
    }
}