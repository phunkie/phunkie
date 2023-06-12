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

final class Failure extends Validation
{
    private $invalid;

    public function __construct($invalid)
    {
        $this->invalid = $invalid;
    }

    public function toString(): string
    {
        return "Failure(" . showValue($this->invalid) . ")";
    }

    public function getOrElse($default)
    {
        return $default;
    }

    public function orElse(Validation $another)
    {
        return $another;
    }

    public function map(callable $f): Kind
    {
        return $this;
    }

    public function fold($fe)
    {
        return fn ($fa) => $fe($this->invalid);
    }

    public function flatten(): Kind
    {
        return $this->invalid;
    }

    public function flatMap(callable $f): Kind
    {
        return $this;
    }

    public function apply(Kind $f): Kind
    {
        if ($f instanceof Failure && is_callable($f->invalid)) {
            return Failure(($f->invalid)($this->invalid));
        }
        return $this;
    }

    public function pure($e): Applicative
    {
        return Failure($e);
    }

    public function map2(Kind $fb, callable $f): Kind
    {
        return $this;
    }
}
