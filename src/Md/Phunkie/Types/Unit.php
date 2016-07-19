<?php

namespace Md\Phunkie\Types;

use Error;
use Md\Phunkie\Cats\Show;

final class Unit extends Tuple
{
    use Show;

    function toString(): string
    {
        return '()';
    }

    public function __get($arg)
    {
        throw new Error("$arg is not a member of Unit");
    }

    public function __set($arg, $ignore)
    {
        throw new Error("$arg is not a member of Unit");
    }

    public function copy()
    {
        throw new Error("copy is not a member of Unit");
    }
}