<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Ops\Option;

use Phunkie\Types\Kind;

trait OptionMonadOps
{
    public function flatMap(callable $f): Kind
    {
        return $this->isEmpty() ? None() : $f($this->get());
    }

    public function flatten(): Kind
    {
        return $this->flatMap(function ($x) {
            return $x;
        });
    }
}
