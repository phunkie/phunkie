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

trait ImmMapEqOps
{
    use Eq;
    public function eqv(self $rhs): bool
    {
        $diff = ImmMap();
        foreach ($rhs->iterator() as $k => $v) {
            if (!($this->contains($k) && $this[$k] == Option($v))) {
                $diff = $diff->plus($k, $v);
            }
        }

        return $diff->iterator()->count() === 0;
    }
}
