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

use function Phunkie\Functions\semigroup\combine;
use Phunkie\Types\Option;
use RuntimeException;

/**
 * @mixin \Phunkie\Types\Option
 */
trait OptionMonoidOps
{
    public function zero()
    {
        return None();
    }

    public function combine(Option $b) { switch (true) {
        case !$this instanceof Option: throw new RuntimeException("Options ops imported to non-option");
        case $this->isEmpty(): return $b;
        case $b->isEmpty(): return $this;
        default: return Some(combine($this->get(), $b->get())); }
    }
}