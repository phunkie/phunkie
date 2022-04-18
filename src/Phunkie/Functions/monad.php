<?php

namespace Phunkie\Functions\monad;

use Phunkie\Cats\FlatMap;
use Phunkie\Cats\Monad as Flatten;
use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\function1\compose;
use const Phunkie\Functions\function1\identity;

const bind = "\\Phunkie\\Functions\\monad\\bind";
function bind($f)
{
    return applyPartially([$f], func_get_args(), function (FlatMap $monad) use ($f) {
        return $monad->flatMap($f);
    });
}

const flatten = "\\Phunkie\\Functions\\monad\\flatten";
function flatten(Flatten $monad)
{
    return $monad->flatten();
}

const mcompose = "\\Phunkie\\Functions\\monad\\mcompose";
function mcompose(...$fs)
{
    switch (count($fs)) {
    case 0: return identity;
    case 1: return bind($fs[0]);
    default: return compose(bind($fs[0]), mcompose(...array_slice($fs, 1))); }
}
