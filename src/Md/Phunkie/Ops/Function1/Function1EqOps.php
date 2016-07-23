<?php

namespace Md\Phunkie\Ops\Function1;

use Md\Phunkie\Algebra\Eq;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Lazy;
use Md\Phunkie\Types\Option;

trait Function1EqOps
{
    use Eq;
    public function eqv(Kind $rhs, Option $arg = null): bool
    {
        return matching(
            on($rhs instanceof Function1)->returns(
                new Lazy(function() use ($arg, $rhs) {
                    return $this->__invoke($arg->getOrElse(null)) == $rhs->__invoke($arg->getOrElse(null));
                })
            ),
            on(_)->returns(false)
        );
    }
}