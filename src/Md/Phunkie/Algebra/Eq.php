<?php

namespace Md\Phunkie\Algebra;

use Md\Phunkie\Types\Kind;

trait Eq
{
    abstract public function eqv(Kind $rhs): bool;
}