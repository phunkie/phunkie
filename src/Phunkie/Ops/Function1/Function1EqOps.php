<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Function1;

use Phunkie\Algebra\Eq;
use Phunkie\Types\Function1;
use Phunkie\Types\Option;

trait Function1EqOps
{
    use Eq;
    public function eqv(self $rhs, Option $arg = null): bool
    {
        if ($rhs instanceof Function1) {
            return $this->__invoke($arg->getOrElse(null)) == $rhs->__invoke($arg->getOrElse(null));
        }
        return false;
    }
}
