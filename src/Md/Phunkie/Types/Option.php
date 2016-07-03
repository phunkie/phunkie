<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\get_value_to_show;
use Md\Phunkie\Ops\Option\OptionApplicativeOps;
use Md\Phunkie\Ops\Option\OptionEqOps;
use Md\Phunkie\Ops\Option\OptionMonadOps;

abstract class Option implements Kind, Applicative
{
    use Show;
    const kind = "Option";
    use OptionApplicativeOps, OptionEqOps, OptionMonadOps;
    abstract public function getOrElse($t);
    abstract public function get();
    abstract public function isDefined(): bool;
    abstract public function isEmpty(): bool;
    public function toString(): string {
        return $this->isEmpty() ? "None" : "Some(". get_value_to_show($this->get()) . ")";
    }
}