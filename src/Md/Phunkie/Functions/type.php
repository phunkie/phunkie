<?php

namespace Md\Phunkie\Functions\type;

use Md\Phunkie\Types\ImmInteger;
use Md\Phunkie\Types\ImmString;

function promote($value)
{
    switch (gettype($value)) {
        case "int":
        case "integer":
            return new ImmInteger($value);
        case "string": return new ImmString($value);
        default: return $value;
    }
}