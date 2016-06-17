<?php

namespace Md\Phunkie\Types;

class Some extends Option
{
    private $t;
    private function __construct($value) { $this->t = $value; }
    private function __clone() {}
    public static function instance($t) { return new Some($t); }
    public function getOrElse($t) { return $this->t; }
    public function get() { return $this->t; }
    public function isDefined(): bool { return true; }
    public function isEmpty(): bool { return false; }
}