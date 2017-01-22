<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmList;

use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use Phunkie\Types\ImmList;
use function Phunkie\Functions\currying\applyPartially;

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