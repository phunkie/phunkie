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

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\ImmList;
use ReturnTypeWillChange;

trait ImmListFunctorOps
{
    use FunctorOps;

    #[ReturnTypeWillChange]
    public function map(callable $f): ImmList
    {
        return ImmList(...array_map(fn ($element) => $f($element), $this->values));
    }

    #[ReturnTypeWillChange]
    public function imap(callable $f, callable $g): ImmList
    {
        return $this->map($f);
    }
}
