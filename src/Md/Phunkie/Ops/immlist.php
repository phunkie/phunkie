<?php

use Md\Phunkie\Types\ImmList;

function ImmList(...$values): ImmList
{
    return new ImmList(...$values);
}