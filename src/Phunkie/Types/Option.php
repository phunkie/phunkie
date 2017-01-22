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

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use function Phunkie\Functions\show\showValue;
use Phunkie\Ops\Option\OptionApplicativeOps;
use Phunkie\Ops\Option\OptionEqOps;
use Phunkie\Ops\Option\OptionFoldableOps;
use Phunkie\Ops\Option\OptionMonadOps;
use Phunkie\Ops\Option\OptionMonoidOps;

abstract class Option implements Kind, Applicative, Monad
{
    use Show;
    const kind = "Option";
    use OptionApplicativeOps,
        OptionEqOps,
        OptionMonadOps,
        OptionFoldableOps,
        OptionMonoidOps;
    abstract public function getOrElse($t);
    abstract public function get();
    abstract public function isDefined();
    abstract public function isEmpty();
    public function toString(): string {
        return $this->isEmpty() ? "None" : "Some(". showValue($this->get()) . ")";
    }
}