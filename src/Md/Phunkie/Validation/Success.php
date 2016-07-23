<?php

namespace Md\Phunkie\Validation;

class Success extends Validation
{
    private $valid;

    public function __construct($valid)
    {
        $this->valid = $valid;
    }
}