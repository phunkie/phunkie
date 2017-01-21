<?php

namespace Md\Phunkie\Ops\ImmList;

use function Md\Phunkie\Functions\semigroup\combine;
use function Md\Phunkie\Functions\semigroup\zero;
use Md\Phunkie\Types\ImmList;
use function Md\Phunkie\Functions\currying\applyPartially;

trait ImmListFoldableOps
{
    public function foldLeft($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            $acc = function(ImmList $xs, $initial) use (&$acc, $f) {
                return $xs->isEmpty() ? $initial : $acc($xs->tail(), $f($initial, $xs->head()));
            };
            return $acc($this, $initial);
        });
    }

    public function foldRight($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            $acc = function (ImmList $xs, $initial) use (&$acc, $f) {
                return $xs->isEmpty() ? $initial : $acc($xs->init(), $f($xs->last(), $initial));
            };
            return $acc($this, $initial);
        });
    }

    public function foldMap(callable $f)
    {
        return $this->foldLeft(zero($this->head()), function($b, $a) use ($f) { return combine($b, $f($a)); });
    }

    public function fold($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            return (!$this->isEmpty()) ? $this->foldLeft($initial, $f) : $initial;
        });
    }
}