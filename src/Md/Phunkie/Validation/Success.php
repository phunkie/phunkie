<?php

namespace Md\Phunkie\Validation;

use function Md\Phunkie\Functions\show\get_value_to_show;

class Success extends Validation
{
    private $valid;

    public function __construct($valid)
    {
        $this->valid = $valid;
    }

    public function toString(): string
    {
        return "Success(" . get_value_to_show($this->valid) . ")";
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