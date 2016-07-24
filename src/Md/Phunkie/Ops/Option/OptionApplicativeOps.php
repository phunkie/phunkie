<?php

namespace Md\Phunkie\Ops\Option;

use BadMethodCallException;
use Md\Phunkie\Types\{Kind, Option, None};

/**
 * @mixin Option
 */
trait OptionApplicativeOps
{
    use OptionFunctorOps;
    public function pure($a): Kind { return Option($a); }
    public function apply(Kind $f): Kind {
        switch (true) {
            case !$this instanceof Option: throw new BadMethodCallException();
            case $this->isEmpty(): return None();
            case $f instanceof None: return None();
            case $f instanceof Option && is_callable($f->get()): return $this->map($f->get());
        }
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }
}