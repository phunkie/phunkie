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

use function Phunkie\Functions\show\showValue;
use Phunkie\Types\Kind;

class Success extends Validation
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

    public function map(callable $f): Kind
    {
        return Success($f($this->valid));
    }

    public function fold($fe)
    {
        return function($fa) { return $fa($this->valid); };
    }
}