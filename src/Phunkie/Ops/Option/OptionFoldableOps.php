<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Option;

use Phunkie\Algebra\Monoid;
use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use Phunkie\Types\Function1;

trait OptionFoldableOps
{
    public function foldLeft($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $f($initial, $this->getOrElse(zero($initial)));
        });
    }

    public function foldRight($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $f($this->getOrElse(zero($initial)), $initial);
        });
    }

    public function foldMap(callable $f)
    {
        $none = md5("None");
        return $this->foldLeft(zero($this->getOrElse($none)), function($b, $a) use ($f, $none) {
            if ($b == $none) $b = zero($a);
            return combine($b, $f($a));
        });
    }

    public function fold($initial)
    {
        return applyPartially([$initial], func_get_args(), function(callable $f) use ($initial) {
            return $this->isDefined() ? $f($this->get()) : $initial;
        });
    }
}