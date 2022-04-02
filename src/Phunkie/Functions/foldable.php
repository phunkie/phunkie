<?php

namespace Phunkie\Functions\foldable;

use Phunkie\Cats\Foldable;
use function Phunkie\Functions\currying\applyPartially;

const foldl = "Phunkie\\Functions\\foldable\\foldl";
function foldl(callable $f)
{
    return applyPartially([$f], func_get_args(), function ($initial) use ($f) {
        return applyPartially([$initial], func_get_args(), function (Foldable $foldable) use ($initial, $f) {
            return $foldable->foldLeft($initial, $f);
        });
    });
}

const foldr = "Phunkie\\Functions\\foldable\\foldr";
function foldr(callable $f)
{
    return applyPartially([$f], func_get_args(), function ($initial) use ($f) {
        return applyPartially([$initial], func_get_args(), function (Foldable $foldable) use ($initial, $f) {
            return $foldable->foldRight($initial, $f);
        });
    });
}

const foldMap = "Phunkie\\Functions\\foldable\\foldMap";
function foldMap(callable $f)
{
    return applyPartially([$f], func_get_args(), function (Foldable $foldable) use ($f) {
        return $foldable->foldMap($f);
    });
}
