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

use const Phunkie\Functions\function1\identity;
use Phunkie\Types\Function1;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;

trait FunctorLaws
{
    public function covariantIdentity(Kind $fa, Option $forArg): bool
    {
        return $fa->eqv($fa->map(identity), $forArg);
    }

    public function covariantComposition(Kind $fa, Function1 $f, Function1 $g): bool
    {
        return $fa->map($f)->map($g)->eqv($fa->map($f->andThen($g)), Some(42));
    }
}