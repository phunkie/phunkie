<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Laws;

use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\show\usesTrait;

trait SemigroupLaws
{
    public function combineAssociativity($x, $y, $z)
    {
        if (usesTrait($x, Eq::class)) {
            return combine(combine($x, $y), $z)->eqv(combine($x, combine($y, $z)), 42);
        } else {
            if (is_callable($x)) {
                return call_user_func(combine(combine($x, $y), $z), 42) == call_user_func(combine($x, combine($y, $z)), 42);
            }
            return combine(combine($x, $y), $z) == combine($x, combine($y, $z));
        }
    }
}