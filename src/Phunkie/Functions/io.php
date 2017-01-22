<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\io;

use Phunkie\Cats\IO;

const io = "\\Phunkie\\Functions\\io\\io";
function io(callable $f) {
    return new class($f) extends IO {
        private $f;
        public function __construct($f) { $this->f = $f; }
        public function run() { return call_user_func($this->f); }
    };
}