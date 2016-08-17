<?php

namespace Md\Phunkie\Algebra;

trait Eq
{
    abstract public function eqv(self $rhs): bool;
}