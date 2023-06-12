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

use function Phunkie\Functions\currying\applyPartially;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;

trait OptionFoldableOps
{
    public function foldLeft($initial)
    {
        return applyPartially([$initial], func_get_args(), fn (callable $f) => $f($initial, $this->getOrElse(zero($initial))));
    }

    public function foldRight($initial)
    {
        return applyPartially([$initial], func_get_args(), fn (callable $f) => $f($this->getOrElse(zero($initial)), $initial));
    }

    public function foldMap(callable $f)
    {
        $none = md5("None");
        return $this->foldLeft(zero($this->getOrElse($none)), function ($b, $a) use ($f, $none) {
            if ($b == $none) {
                $b = zero($a);
            }
            return combine($b, $f($a));
        });
    }

    public function fold($initial)
    {
        return applyPartially([$initial], func_get_args(), fn (callable $f) => $this->isDefined() ? $f($this->get()) : $initial);
    }
}
