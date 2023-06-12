<?php

namespace Phunkie\Cats;

use Phunkie\Types\Kind;
use function Phunkie\Functions\applicative\pure as point;

use function Phunkie\Functions\monad\bind as flatMap;
use function Phunkie\PatternMatching\Referenced\Pure;
use function Phunkie\PatternMatching\Referenced\Suspend;
use function Phunkie\PatternMatching\Referenced\Bind;

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

    public function foldMap(NaturalTransformation $nt) { $on = pmatch($this); return match (true) {
        $on(Pure($a)) => (point($nt->to))($a),
        $on(Suspend($fa)) => $nt($fa),
        $on(Bind($target, $f)) => flatMap(
            fn ($e) => $f($e)->foldMap($nt),
            $target->foldMap($nt)
        )};
    }
}
