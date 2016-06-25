<?php

namespace Md\Phunkie\Algebra;

use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

trait Eq
{
    abstract public function eqv(Kind $rhs): bool;
}