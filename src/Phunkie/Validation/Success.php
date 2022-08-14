<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Validation;

use Phunkie\Cats\Applicative;
use Phunkie\Types\Kind;
use function Phunkie\Functions\show\showValue;
use const Phunkie\Functions\function1\identity;

final class Success extends Validation
{
    private $valid;

    public function __construct($valid)
    {
        $this->valid = $valid;
    }

    public function toString(): string
    {
        return "Success(" . showValue($this->valid) . ")";
    }

    public function getOrElse($default)
    {
        return $this->valid;
    }

    public function orElse(Validation $ignored)
    {
        return $this;
    }

    public function map(callable $f): Kind
    {
        return Success($f($this->valid));
    }

    public function fold($fe)
    {
        return fn ($fa) => $fa($this->valid);
    }

    public function flatten(): Kind
    {
        return $this->flatMap(identity);
    }

    public function flatMap(callable $f): Kind
    {
        return $f($this->valid);
    }

    public function apply(Kind $f): Kind
    {
        switch (true) {
            case $f instanceof Success && is_callable($f->valid): return Success(($f->valid)($this->valid));
            case $f instanceof Failure && !is_callable(($f->fold(identity))(_)): return $f;
        }
    }

    public function pure($a): Applicative
    {
        return Success($a);
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this->apply($fb->map(fn ($b) => fn ($a) => $f($a, $b)));
    }
}
