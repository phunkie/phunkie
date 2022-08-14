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

    use Phunkie\Types\ImmMap;
    use Phunkie\Types\ImmSet;
    use Phunkie\Types\Option;
    use Phunkie\Types\Pair;
    use Phunkie\Utils\GenLens;
    use function Phunkie\PatternMatching\Referenced\Some as Maybe;
    use const Phunkie\Functions\function1\identity;

    const trivial = "\\Phunkie\\Functions\\lens\\lens";
    function trivial()
    {
        return Lens(
            fn ($a) => Unit(),
            fn ($ignore, $a) => $a
        );
    }

    const self = "\\Phunkie\\Functions\\lens\\self";
    function self()
    {
        return Lens(
            identity,
            fn ($a, $ignore) => $a
        );
    }

    const fst = "\\Phunkie\\Functions\\lens\\fst";
    function fst()
    {
        return Lens(
            fn (Pair $p) => $p->_1,
            fn ($a, Pair $p) => $p->copy(["_1" => $a])
        );
    }

    const snd = "\\Phunkie\\Functions\\lens\\snd";
    function snd()
    {
        return Lens(
            fn (Pair $p) => $p->_2,
            fn ($b, Pair $p) => $p->copy(["_2" => $b])
        );
    }

    const contains = "\\Phunkie\\Functions\\lens\\contains";
    function contains($element)
    {
        return Lens(
            fn (ImmSet $s) => $s->contains($element),
            function (ImmSet $s, bool $plusOrMinus) use ($element) {
                switch ($plusOrMinus) {
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
            fn (ImmMap $m) => $m->get($k),
            function (ImmMap $m, Option $v) use ($k) {
                $on = pmatch($v);
                switch (true) {
                case $on(None): return $m->minus($k);
                case $on(Maybe($v)): return $m->minus($k)->plus($k, $v);}
            }
        );
    }

    const makeLenses = "\\Phunkie\\Functions\\lens\\makeLenses";
    function makeLenses(...$fields)
    {
        return new GenLens(...$fields);
    }
}
