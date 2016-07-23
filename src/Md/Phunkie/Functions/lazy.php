<?php

use Md\Phunkie\Types\Lazy;

function Lazy(callable $f)
{
    return new Lazy($f);
}