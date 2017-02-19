<?php

namespace Phunkie\Functions\trampoline;

use Phunkie\Utils\Trampoline\More;
use Phunkie\Utils\Trampoline\Done;

function More(callable $k)
{
    return new More($k);
}

function Done($v)
{
    return new Done($v);
}