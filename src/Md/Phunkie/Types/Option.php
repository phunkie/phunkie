<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Ops\Option\OptionEqOps;
use Md\Phunkie\Ops\Option\OptionFunctorOps;

abstract class Option implements Kind
{
    use OptionFunctorOps, OptionEqOps;
    abstract public function getOrElse($t);
    abstract public function get();
    abstract public function isDefined(): bool;
    abstract public function isEmpty(): bool;
}