<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use function Phunkie\Functions\semigroup\combine;

final class NonEmptyList extends ImmList
{
    public function combine(ImmList $another)
    {
        return Nel(...combine($this->toArray(), $another->toArray()));
    }

    public function failure()
    {
        return Failure($this);
    }

    public function success()
    {
        return Success($this);
    }
}
