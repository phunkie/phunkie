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
    public function getOrElse($t)
    {
        return $this->t;
    }
    public function get() { return $this->t; }
    public function isDefined(): bool { return true; }
    public function isEmpty(): bool { return false; }
}