<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\get_value_to_show;

class Failure extends Validation
{
    private $invalid;

    public function __construct($invalid)
    {
        $this->invalid = $invalid;
    }

    public function toString(): string
    {
        return "Failure(" . get_value_to_show($this->invalid) . ")";
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