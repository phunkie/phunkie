<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\ImmMap;

use Phunkie\Algebra\Eq;
use Phunkie\Types\ImmInteger;
use Phunkie\Types\ImmString;

trait ImmMapEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        foreach ($this->values as $offset) {
            if ($offset instanceof ImmInteger || $offset instanceof ImmString) {
                $offset = $offset->get();
            }
            if (!$rhs->contains($offset)) {
                return false;
            }
        }
        return true;
    }
}