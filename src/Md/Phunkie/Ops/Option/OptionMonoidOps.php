<?php


namespace Md\Phunkie\Ops\Option;


use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
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

    public function combine(Option $b)
    {
        return matching(
            on(!$this instanceof Option)->throws(new RuntimeException("Options ops imported to non-option")),
            on($this->isEmpty())->returns($b),
            on($b->isEmpty())->returns($this),
            on(_)->returns(Lazy(function() use ($b) { return Some(combine($this->get(), $b->get())); }))
        );
    }
}