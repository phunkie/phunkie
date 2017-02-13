<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Cats\Lens;

    function Lens(callable $g, callable $s)
    {
        return new Lens($g, $s);
    }
}

namespace Phunkie\Functions\lens {

    use const Phunkie\Functions\function1\identity;
    use function Phunkie\PatternMatching\Referenced\Some as Maybe;
    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmSet;
    use Phunkie\Types\Option;
    use Phunkie\Types\Pair;
    use Phunkie\Utils\GenLens;

    const trivial = "\\Phunkie\\Functions\\lens\\lens";
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

    const self = "\\Phunkie\\Functions\\lens\\self";
    function self()
    {
        return Lens(
            identity,
            function ($a, $ignore) {
                return $a;
            }
        );
    }

    const fst = "\\Phunkie\\Functions\\lens\\fst";
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

    const snd = "\\Phunkie\\Functions\\lens\\snd";
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

    const contains = "\\Phunkie\\Functions\\lens\\contains";
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

    const member = "\\Phunkie\\Functions\\lens\\member";
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

    const makeLenses = "\\Phunkie\\Functions\\lens\\makeLenses";
    function makeLenses(...$fields)
    {
        return new GenLens(...$fields);
    }
}