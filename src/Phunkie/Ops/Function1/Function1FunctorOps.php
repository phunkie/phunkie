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

use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;

/**
 * @mixin \Phunkie\Types\Function1
 */
trait Function1FunctorOps
{
    use FunctorOps;
    public function map(callable $f): Kind
    {
        return $this->andThen($f);
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}