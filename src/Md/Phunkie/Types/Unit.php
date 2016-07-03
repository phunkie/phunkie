<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;

final class Unit
{
    use Show;

    function toString(): string
    {
        return '()';
    }
}