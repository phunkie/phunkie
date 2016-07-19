<?php

namespace Md\Phunkie\Types;

final class Some extends Option
{
    private $t;
    private function __construct($value) { $this->t = $value; }
    private function __clone() {}
    public static function instance()
    {
        $numArgs = func_num_args();
        switch ($numArgs) {
            case 0: return new Some(Unit());
            case 1: return new Some(func_get_arg(0));
            default: throw new \TypeError(sprintf("Option must take exactly 1 argument, %d given", $numArgs));
        }
    }
    public function getOrElse($t) { return $this->t; }
    public function get() { return $this->t; }
    public function isDefined(): bool { return true; }
    public function isEmpty(): bool { return false; }
}