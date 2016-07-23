<?php

namespace Md\Phunkie\Ops\Option;

use BadMethodCallException;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\{Kind, Option, None};

/**
 * @mixin Option
 */
trait OptionApplicativeOps
{
    use OptionFunctorOps;
    public function pure($a): Kind { return Option($a); }
    public function apply(Kind $f): Kind {
        return matching(
            on(!$this instanceof Option)->throws(new BadMethodCallException()),
            on($this->isEmpty())->returns(None()),
            on($f instanceof None)->returns(None()),
            on($f instanceof Option && is_callable($f->get()))->
                returns($this->map($f->get()))
        );
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(function($b) use ($f) { return function($a) use ($f, $b) { return $f($a, $b);};}));
    }
}