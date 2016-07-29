<?php

namespace Md\Phunkie\Validation;

trait Validated
{
    public function success()
    {
        return new Success($this);
    }
}