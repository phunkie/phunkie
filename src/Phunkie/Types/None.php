<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

final class None extends Option
{
    private static $instance;
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
    public function showType(): string { return "None"; }
}