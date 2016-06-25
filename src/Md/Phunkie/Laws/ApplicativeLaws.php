<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Kind;
use Md\Phunkie\Types\Option;

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

    public function applicativeInterchange(Kind $fa, $a, Kind $ff): bool
    {
        return $fa->pure($a)->apply($ff) == $ff->apply($fa->pure(function($f) use($a) { return $f($a); }));
    }

    public function applicativeMap(Kind $fa, callable $f): bool
    {
        return $fa->map($f) == $fa->apply($fa->pure($f));
    }
}