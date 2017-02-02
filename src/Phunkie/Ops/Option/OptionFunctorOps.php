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

use Phunkie\Cats\Functor;
use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;
use Phunkie\Types\None;

/**
 * @mixin \Phunkie\Types\Some
 */
trait OptionFunctorOps
{
    use FunctorOps;

    public function map(callable $f): Kind { switch (true) {
        case $this->isEmpty(): return None();
        case ($f($this->get()) instanceof None || $f($this->get()) === null): return None();
        default: return Some($f($this->get())); }
    }

    public function imap(callable $f,callable $g): Kind
    {
        return $this->map($f);
    }
}