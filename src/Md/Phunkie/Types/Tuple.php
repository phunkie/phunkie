<?php

namespace Md\Phunkie\Types;

use Error;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\functor\map;
use function Md\Phunkie\Functions\show\get_value_to_show;

class Tuple
{
    use Show;
    private $values;

    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function __get($arg)
    {
        if (strpos($arg, "_") !== 0) {
            throw new Error("$arg is not a member of Tuple");
        }

        $key = substr($arg, 1);
        if (!is_numeric($key)) {
            throw new Error("$arg is not a member of Tuple");
        }

        $key = ((integer) $key) - 1;
        if (!array_key_exists($key, $this->values)) {
            throw new Error("$arg is not a member of Tuple");
        }

        return $this->values[$key];
    }

    public function __set($arg, $value)
    {
        throw new \TypeError("Tuples are immutable");
    }

    public function toString(): string
    {
        return "(" . implode(", ", map("\\Md\\Phunkie\\Functions\\show\\get_value_to_show", ImmList(...($this->values)))->toArray()) . ")";
    }

    public function getArity()
    {
        return count($this->values);
    }
}