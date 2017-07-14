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
use Phunkie\Types\Kind;

trait ImmListFunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return ImmList(...array_map(function($element) use ($f){
            return $f($element);
        }, $this->values));
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}