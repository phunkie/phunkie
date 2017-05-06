<?php

namespace Phunkie\Cats;

use function Phunkie\Functions\applicative\pure as point;
use function Phunkie\Functions\monad\bind as flatMap;

use function Phunkie\PatternMatching\Referenced\Pure;
use function Phunkie\PatternMatching\Referenced\Suspend;
use function Phunkie\PatternMatching\Referenced\Bind;
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
        case $on(Suspend($fa)): return $nt($fa);
        case $on(Bind($target, $f)):
            return flatMap (function($e) use ($f, $nt) { return $f($e)->foldMap($nt); },
                     $target->foldMap($nt));
        }
    }
}
