<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\get_value_to_show;
use Md\Phunkie\Ops\Option\OptionEqOps;
use Md\Phunkie\Ops\Option\OptionFunctorOps;

abstract class Option implements Kind
{
    use Show;
    const kind = "Option";
    use OptionFunctorOps, OptionEqOps;
    abstract public function getOrElse($t);
    abstract public function get();
    abstract public function isDefined(): bool;
    abstract public function isEmpty(): bool;
    public function toString(): string {
        return $this->isEmpty() ? "None" : "Some(". get_value_to_show($this->get()) . ")";
    }
}