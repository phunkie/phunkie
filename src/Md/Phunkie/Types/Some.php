<?php

namespace Md\Phunkie\Types;

final class Some extends Option
{
    private $t;
    private function __construct($value) { $this->t = $value; }
    private function __clone() {}
    public static function instance($t) { self::guardNumArgs(func_num_args()); return new Some($t); }
    public function getOrElse($t) { return $this->t; }
    public function get() { return $this->t; }
    public function isDefined(): bool { return true; }
    public function isEmpty(): bool { return false; }

    private static function guardNumArgs(int $numArgs)
    {
        if ($numArgs != 1) {
            throw new \TypeError(sprintf("Option must take exactly 1 argument, %d given", $numArgs));
        }
    }
}