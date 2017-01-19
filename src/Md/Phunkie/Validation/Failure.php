<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\showValue;

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

    public function map($f)
    {
        return $f($this->invalid);
    }
}