<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use Md\Phunkie\Ops\Option\OptionApplicativeOps;
use Md\Phunkie\Ops\Option\OptionEqOps;
use Md\Phunkie\Ops\Option\OptionFoldableOps;
use Md\Phunkie\Ops\Option\OptionMonadOps;
use Md\Phunkie\Ops\Option\OptionMonoidOps;

abstract class Option implements Kind, Applicative
{
    use Show;
    const kind = "Option";
    use OptionApplicativeOps,
        OptionEqOps,
        OptionMonadOps,
        OptionFoldableOps,
        OptionMonoidOps;
    abstract public function getOrElse($t);
    abstract public function get();
    abstract public function isDefined();
    abstract public function isEmpty();
    public function toString(): string {
        return $this->isEmpty() ? "None" : "Some(". get_value_to_show($this->get()) . ")";
    }
}