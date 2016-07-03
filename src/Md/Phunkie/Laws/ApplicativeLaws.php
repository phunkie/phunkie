<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Types\Kind;

trait ApplicativeLaws
{
    public function applicativeIdentity(Kind $fa): bool
    {
        return $fa->apply($fa->pure(function($x) {return $x;}))->eqv($fa, Some(42));
    }

    public function applicativeHomomorphism(Kind $fa, $a, callable $f): bool
    {
        return $fa->pure($a)->apply($fa->pure($f))->eqv($fa->pure($f($a)), Some(42));
    }

    public function applicativeInterchange(Kind $fa, $a, Kind $fab): bool
    {
        return $fa->pure($a)
                  ->apply($fab)->eqv(

               $fab->apply(
                   $fa->pure(
                       function($f) use($a) { return $f($a); }
                   )
               ), Some(42));
    }

    public function applicativeMap(Kind $fa, callable $f): bool
    {
        return $fa->map($f)->eqv($fa->apply($fa->pure($f)), Some(42));
    }
}