<?php

namespace Phunkie\Cats;

use function Phunkie\PatternMatching\Referenced\Pure;
use function Phunkie\Functions\applicative\pure as point;

use Phunkie\Types\Kind;

abstract class Free
{
    public static function pure($a)
    {
        return new Free\Pure($a);
    }

    public static function liftM(Kind $fa)
    {
        return new Free\Suspend($fa);
    }

    public function flatMap($f)
    {
        return new Free\Bind($this, $f);
    }

    public function foldMap(NaturalTransformation $nt) { $on = match($this); switch(true) {
        case $on(Pure($a)): return (point($nt->to)) ($a);
        }
    }
}
