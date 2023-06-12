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
    return applyPartially([$f], func_get_args(), fn (FlatMap $monad) => $monad->flatMap($f));
}

const flatten = "\\Phunkie\\Functions\\monad\\flatten";
function flatten(Flatten $monad)
{
    return $monad->flatten();
}

const mcompose = "\\Phunkie\\Functions\\monad\\mcompose";
function mcompose(...$fs) { return match (count($fs)) {
    0 => identity,
    1 => bind($fs[0]),
    default => compose(bind($fs[0]), mcompose(...array_slice($fs, 1))) };
}
