<?php

use Md\Phunkie\Types\Pair;
use Md\Phunkie\Types\Tuple;
use Md\Phunkie\Types\Unit;

function Tuple(...$values)
{
    switch(count($values)) {
        case 0:
            return new Unit();
        case 2:
            return new Pair($values[0], $values[1]);
        default:
            return new Tuple(...$values);
    }
}