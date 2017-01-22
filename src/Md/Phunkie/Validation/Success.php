<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\showValue;
use Md\Phunkie\Types\Kind;

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

    public function fold($fe, $fa)
    {
        return $fa($this->valid);
    }
}