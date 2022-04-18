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

use Phunkie\Types\ImmList;
use function Phunkie\Functions\immlist\concat;

trait ImmListMonoidOps
{
    public function zero()
    {
        return Nil();
    }

    public function combine(ImmList $b)
    {
        return concat($this, $b);
    }
}
