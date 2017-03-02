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

use const Phunkie\Functions\function1\identity;
use function Phunkie\Functions\show\showValue;
use Phunkie\Types\Kind;

class Failure extends Validation
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

    public function map(callable $f): Kind
    {
        return $this;
    }

    public function fold($fe)
    {
        return function($fa) use ($fe) { return $fe($this->invalid); };
    }

    public function flatten(): Kind
    {
        return $this->invalid;
    }

    public function flatMap(callable $f): Kind
    {
        return $this;
    }
}