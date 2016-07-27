<?php

namespace Md\Phunkie\Types;

final class None extends Option
{
    private static $instance;
    private function __construct() {}
    private function __clone() {}
    public static function instance() {
        return self::$instance == null ? self::$instance = new None() : self::$instance;
    }
    public function getOrElse($t)
    {
        return $t;
    }
    public function get() { throw new \RuntimeException("Illegal get() call on None"); }
    public function isDefined(): bool { return false; }
    public function isEmpty():bool { return true; }
}