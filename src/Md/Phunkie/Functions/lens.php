<?php

namespace {

    use Md\Phunkie\Cats\Lens;

    function Lens(callable $g, callable $s)
    {
        return new Lens($g, $s);
    }
}

namespace Md\Phunkie\Functions\lens {

    use const Md\Phunkie\Functions\function1\identity;
    use function Md\Phunkie\PatternMatching\Referenced\Some as Maybe;
    use Md\Phunkie\Types\ImmMap;
    use Md\Phunkie\Types\ImmSet;
    use Md\Phunkie\Types\Option;
    use Md\Phunkie\Types\Pair;

    function trivial()
    {
        return Lens(
            function ($a) {
                return Unit();
            },
            function ($ignore, $a) {
                return $a;
            }
        );
    }

    function self()
    {
        return Lens(
            identity,
            function ($a, $ignore) {
                return $a;
            }
        );
    }

    function fst()
    {
        return Lens(
            function (Pair $p) {
                return $p->_1;
            },
            function ($a, Pair $p) {
                return $p->copy(["_1" => $a]);
            }
        );
    }

    function snd()
    {
        return Lens(
            function (Pair $p) {
                return $p->_2;
            },
            function ($b, Pair $p) {
                return $p->copy(["_2" => $b]);
            }
        );
    }

    function contains($element)
    {
        return Lens(
            function(ImmSet $s) use ($element) { return $s->contains($element); },
            function(ImmSet $s, bool $plusOrMinus) use ($element) { switch($plusOrMinus) {
                case true: return $s->plus($element);
                case false: return $s->minus($element);
                }
            }
        );
    }

    function member($k)
    {
        return Lens(
            function(ImmMap $m) use ($k) { return $m->get($k); },
            function(ImmMap $m, Option $v) use ($k) { $on = match($v); switch(true) {
                case $on(None): return $m->minus($k);
                case $on(Maybe($v)): return $m->plus($k, $v);}
            }
        );
    }
}