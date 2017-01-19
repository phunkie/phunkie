<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function \Md\Phunkie\Functions\show\showValue;
use Md\Phunkie\Utils\Copiable;

final class Pair implements Copiable
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

    public function copy(array $parameters)
    {
        $ta = $this->ta;
        $tb = $this->tb;
        foreach ($parameters as $parameter => $value) {
            switch ($parameter) {
                case "_1": $ta = $value;break;
                case "_2": $tb = $value;break;
                default: throw new \InvalidArgumentException("$parameter is not a member of pair.");
            }
        }
        return new Pair($ta, $tb);
    }

    public function getArity()
    {
        return 2;
    }

    function toString(): string
    {
        return "Pair(" . showValue($this->ta) . ", " . showValue($this->tb) . ")";
    }

    private function guardNumArgs(int $numArgs)
    {
        if ($numArgs != 2) {
            throw new \TypeError(sprintf("Pair must take exactly 2 arguments %d given", $numArgs));
        }
    }
}