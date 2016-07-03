<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Types\Kind;

trait MonadLaws
{
    /**
     * @param Kind<TA> $fa
     * @param TA => Kind<TB> $f
     * @param TB => Kind<TC> $g
     * @return bool
     */
    public function flapMapAssociativity(Kind $fa, callable $f, callable $g): bool
    {
        return $fa->flatMap($f)->flatMap($g) == $fa->flatMap( function($a) use ($f,$g) { return $f($a)->flatMap( function($b) use ($g) { return $g($b); } ) ;} );
    }

    public function leftIdentity(Kind $fa, $a, callable $f): bool
    {
        return $fa->pure($a)->flatMap($f) == $f($a);
    }

    public function rightIdentity(Kind $fa): bool
    {
        return $fa->flatMap(function($a) use ($fa) { return $fa->pure($a); }) == $fa;
    }
}