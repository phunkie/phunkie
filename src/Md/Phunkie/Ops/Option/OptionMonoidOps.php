<?php

namespace Md\Phunkie\Ops\Option;

use function Md\Phunkie\Functions\semigroup\combine;
use Md\Phunkie\Types\Option;
use RuntimeException;

/**
 * @mixin \Md\Phunkie\Types\Option
 */
trait OptionMonoidOps
{
    public function zero()
    {
        return None();
    }

    public function combine(Option $b) { switch (true) {
        case !$this instanceof Option: throw new RuntimeException("Options ops imported to non-option");
        case $this->isEmpty(): return $b;
        case $b->isEmpty(): return $this;
        default: return Some(combine($this->get(), $b->get())); }
    }
}