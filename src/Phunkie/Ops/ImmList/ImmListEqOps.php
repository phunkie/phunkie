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

use Phunkie\Algebra\Eq;

trait ImmListEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        return $this->toArray() == $rhs->toArray();
    }
}
