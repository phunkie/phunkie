<?php

namespace Md\Phunkie\Types;

use Error;
use Md\Phunkie\Cats\Show;

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

        if (!is_numeric(substr($arg, 1))) {
            throw new Error("$arg is not a member of Tuple");
        }

        $key = ((integer) substr($arg, 1)) - 1;
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
        return "(" . implode(",", $this->values) . ")";
    }
}