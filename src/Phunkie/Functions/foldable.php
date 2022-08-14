<?php

namespace Phunkie\Functions\foldable;

use Phunkie\Cats\Foldable;
use function Phunkie\Functions\currying\applyPartially;

const foldl = "Phunkie\\Functions\\foldable\\foldl";
function foldl(callable $f)
{
    return applyPartially([$f], func_get_args(), fn ($initial) => applyPartially([$initial], func_get_args(), fn (Foldable $foldable) => $foldable->foldLeft($initial, $f)));
}

const foldr = "Phunkie\\Functions\\foldable\\foldr";
function foldr(callable $f)
{
    return applyPartially([$f], func_get_args(), fn ($initial) => applyPartially([$initial], func_get_args(), fn (Foldable $foldable) => $foldable->foldRight($initial, $f)));
}

const foldMap = "Phunkie\\Functions\\foldable\\foldMap";
function foldMap(callable $f)
{
    return applyPartially([$f], func_get_args(), fn (Foldable $foldable) => $foldable->foldMap($f));
}
