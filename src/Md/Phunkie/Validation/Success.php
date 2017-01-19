<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\showValue;

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

    public function map($f)
    {
        return $f($this->valid);
    }
}