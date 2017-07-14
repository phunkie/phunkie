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

use Phunkie\Algebra\Eq;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use function \Phunkie\Functions\show\usesTrait;

trait MonoidLaws
{
    use SemigroupLaws;

    public function combineRightIdentity($x): bool
    {
        if (usesTrait($x, Eq::class)) {
            return combine($x, zero($x))->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine($x, zero($x)) == $x;
        }
    }

    public function combineLeftIdentity($x)
    {
        if (usesTrait($x, Eq::class)) {
            return combine(zero($x), $x)->eqv($x, Some(42));
        } else {
            if (is_callable($x)) {
                return call_user_func(combine($x, zero($x)), 42) == $x(42);
            }
            return combine(zero($x), $x) == $x;
        }
    }
}