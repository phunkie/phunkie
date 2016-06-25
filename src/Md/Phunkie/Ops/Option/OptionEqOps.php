<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Algebra\Eq;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait OptionEqOps
{
    use Eq;
    public function eqv(Kind $rhs): bool
    {
        return $this == $rhs;
    }
}