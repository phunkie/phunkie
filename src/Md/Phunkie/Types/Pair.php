<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function \Md\Phunkie\Functions\show\showValue;

final class Pair extends Tuple
{
    use Show;

    public function __get($i)
    {
        switch($i) {
            case "_1": return parent::__get("_1"); break;
            case "_2": return parent::__get("_2"); break;
        }
        throw new \InvalidArgumentException("Invalid index $i for pair");
    }

    public function __set($i, $value)
    {
        throw new \TypeError("Pairs are immutable");
    }

    function toString(): string
    {
        return "Pair(" . showValue(parent::__get("_1")) . ", " . showValue(parent::__get("_2")) . ")";
    }
}