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
    use Md\Phunkie\Utils\GenLens;

    const trivial = "\\Md\\Phunkie\\Functions\\lens\\lens";
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

    const self = "\\Md\\Phunkie\\Functions\\lens\\self";
    function self()
    {
        return Lens(
            identity,
            function ($a, $ignore) {
                return $a;
            }
        );
    }

    const fst = "\\Md\\Phunkie\\Functions\\lens\\fst";
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

    const snd = "\\Md\\Phunkie\\Functions\\lens\\snd";
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

    const contains = "\\Md\\Phunkie\\Functions\\lens\\contains";
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

    const member = "\\Md\\Phunkie\\Functions\\lens\\member";
    function member($k)
    {
        return Lens(
            function(ImmMap $m) use ($k) { return $m->get($k); },
            function(ImmMap $m, Option $v) use ($k) { $on = match($v); switch(true) {
                case $on(None): return $m->minus($k);
                case $on(Maybe($v)): return $m->minus($k)->plus($k, $v);}
            }
        );
    }

    const makeLenses = "\\Md\\Phunkie\\Functions\\lens\\makeLenses";
    function makeLenses(...$fields)
    {
        return new GenLens(...$fields);
    }
}