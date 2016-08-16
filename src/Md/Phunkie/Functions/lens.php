<?php

namespace Md\Phunkie\Functions\lens;

use Md\Phunkie\Cats\Lens;
use const Md\Phunkie\Functions\function1\identity;
use Md\Phunkie\Types\Pair;

function trivial()
{
    return new Lens(
        function($a) {return Unit();},
        function($ignore, $a) {return $a;}
    );
}

function self()
{
    return new Lens(
        identity,
        function($a, $ignore) {return $a;}
    );
}

function fst()
{
    return new Lens(
        function(Pair $p) { return $p->_1;},
        function($a, Pair $p) { return $p->copy(["_1" => $a]); }
    );
}

function snd()
{
    return new Lens(
        function(Pair $p) { return $p->_2;},
        function($b, Pair $p) { return $p->copy(["_2" => $b]); }
    );
}