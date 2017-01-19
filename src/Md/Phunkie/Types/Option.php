<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Applicative;
use Md\Phunkie\Cats\Monad;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\showValue;
use Md\Phunkie\Ops\Option\OptionApplicativeOps;
use Md\Phunkie\Ops\Option\OptionEqOps;
use Md\Phunkie\Ops\Option\OptionFoldableOps;
use Md\Phunkie\Ops\Option\OptionMonadOps;
use Md\Phunkie\Ops\Option\OptionMonoidOps;

abstract class Option implements Kind, Applicative, Monad
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
        return $this->isEmpty() ? "None" : "Some(". showValue($this->get()) . ")";
    }
}