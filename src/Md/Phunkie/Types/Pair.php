<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function \Md\Phunkie\Functions\show\get_value_to_show;

final class Pair
{
    use Show;
    private $ta;
    private $tb;

    public function __construct($ta, $tb)
    {
        $this->guardNumArgs(func_num_args());
        $this->ta = $ta;
        $this->tb = $tb;
    }

    public function __get($i)
    {
        switch($i) {
            case "_1": return $this->ta; break;
            case "_2": return $this->tb; break;
        }
        throw new \InvalidArgumentException("Invalid index $i for pair");
    }

    public function __set($i, $value)
    {
        throw new \TypeError("Pairs are immutable");
    }

    public function getArity()
    {
        return 2;
    }

    function toString(): string
    {
        return "Pair(" . get_value_to_show($this->ta) . "," . get_value_to_show($this->tb) . ")";
    }

    private function guardNumArgs(int $numArgs)
    {
        if ($numArgs != 2) {
            throw new \TypeError(sprintf("Pair must take exactly 2 arguments %d given", $numArgs));
        }
    }
}