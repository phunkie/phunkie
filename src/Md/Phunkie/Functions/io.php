<?php

namespace Md\Phunkie\Functions\io;

use Md\Phunkie\Cats\IO;

const io = "\\Md\\Phunkie\\Functions\\io\\io";
function io(callable $f) {
    return new class($f) extends IO {
        private $f;
        public function __construct($f) { $this->f = $f; }
        public function run() { return call_user_func($this->f); }
    };
}